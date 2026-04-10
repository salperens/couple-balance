<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Ai\Agents\ChatSummarizerAgent;
use App\Models\AiAssistantChatSummary;
use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistant\AiAssistantLimits;
use App\Support\AiAssistant\AiAssistantLogEvent;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class SummarizeAiAssistantChatAction
{
    public function execute(User $user): void
    {
        $previous = AiAssistantChatSummary::query()
            ->where('user_id', $user->id)
            ->value('summary');

        $window = (int) config('ai_assistant.limits.context_messages', AiAssistantLimits::CONTEXT_MESSAGES);

        $lastWindow = AiAssistantMessage::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit($window)
            ->get()
            ->sortBy('id')
            ->values();

        if ($lastWindow->isEmpty()) {
            return;
        }

        $labels = config('relationship_coach.summarizer_labels', []);
        $userLabel = is_array($labels) && isset($labels['user']) ? (string) $labels['user'] : 'Kullanıcı';
        $assistantLabel = is_array($labels) && isset($labels['assistant']) ? (string) $labels['assistant'] : 'Asistan';

        $lines = $lastWindow->map(function (AiAssistantMessage $m) use ($userLabel, $assistantLabel): string {
            $who = $m->type === AiAssistantMessage::TYPE_USER ? $userLabel : $assistantLabel;

            return $who.': '.$m->text;
        })->implode("\n");

        $prompt = config('relationship_coach.summarizer_prompt', []);
        $blocks = is_array($prompt) ? $prompt : [];

        $body = '';
        if (is_string($previous) && $previous !== '') {
            $body .= sprintf((string) ($blocks['previous_summary_block'] ?? "Önceki özet:\n%s\n\n"), $previous);
        }
        $body .= sprintf(
            (string) ($blocks['recent_messages_block'] ?? "Son %d mesaj:\n%s\n\n"),
            $window,
            $lines
        );
        $body .= (string) ($blocks['task_suffix'] ?? '');

        $provider = config('relationship_coach.summarizer_provider') ?: config('relationship_coach.provider', config('ai.default', 'openai'));
        $model = config('relationship_coach.summarizer_model');

        try {
            $response = (new ChatSummarizerAgent)->prompt(
                $body,
                [],
                is_string($provider) ? $provider : 'openai',
                is_string($model) && $model !== '' ? $model : null,
            );
            $text = trim((string) $response);
            if ($text === '') {
                return;
            }

            AiAssistantChatSummary::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['summary' => $text]
            );
        } catch (Throwable $e) {
            Log::warning(AiAssistantLogEvent::SUMMARIZE_FAILED, [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
