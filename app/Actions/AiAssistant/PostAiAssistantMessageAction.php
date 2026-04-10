<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Ai\Agents\RelationshipCoachAgent;
use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistant\AiAssistantLimits;
use App\Support\AiAssistant\AiAssistantLogEvent;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class PostAiAssistantMessageAction
{
    public function __construct(
        private SummarizeAiAssistantChatAction $summarizeAiAssistantChatAction,
    ) {}

    /**
     * @return array{user: AiAssistantMessage, assistant: AiAssistantMessage}
     */
    public function execute(User $user, string $text): array
    {
        $rollingSummary = $user->aiAssistantChatSummary?->summary;

        $userMessage = AiAssistantMessage::query()->create([
            'user_id' => $user->id,
            'type' => AiAssistantMessage::TYPE_USER,
            'text' => $text,
        ]);

        $agent = new RelationshipCoachAgent($user, $userMessage->id, $rollingSummary);

        try {
            $response = $agent->prompt($text);
            $assistantText = trim((string) $response);
            if ($assistantText === '') {
                $assistantText = (string) config('relationship_coach.fallback_reply');
            }
        } catch (Throwable $e) {
            Log::warning(AiAssistantLogEvent::COACH_FAILED, [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
            $assistantText = (string) config('relationship_coach.fallback_reply');
        }

        $assistantMessage = AiAssistantMessage::query()->create([
            'user_id' => $user->id,
            'type' => AiAssistantMessage::TYPE_ASSISTANT,
            'text' => $assistantText,
        ]);

        $totalCount = AiAssistantMessage::query()->where('user_id', $user->id)->count();
        $interval = (int) config('ai_assistant.limits.summary_interval', AiAssistantLimits::SUMMARY_INTERVAL);

        if ($totalCount > 0 && $interval > 0 && $totalCount % $interval === 0) {
            $this->summarizeAiAssistantChatAction->execute($user);
        }

        return [
            'user' => $userMessage->fresh(),
            'assistant' => $assistantMessage->fresh(),
        ];
    }
}
