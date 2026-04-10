<?php

declare(strict_types=1);

namespace App\Actions\AiAssistant;

use App\Ai\Agents\RelationshipCoachAgent;
use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistant\AiAssistantLimits;
use App\Support\AiAssistant\AiAssistantLogEvent;
use Illuminate\Support\Arr;
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
    public function execute(User $user, string $text, bool $simulateAssistant = false): array
    {
        $userMessage = AiAssistantMessage::query()->create([
            'user_id' => $user->id,
            'type' => AiAssistantMessage::TYPE_USER,
            'text' => $text,
        ]);

        if ($simulateAssistant) {
            $assistantText = $this->randomDebugReply();
        } else {
            $rollingSummary = $user->aiAssistantChatSummary?->summary;
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
        }

        $assistantMessage = AiAssistantMessage::query()->create([
            'user_id' => $user->id,
            'type' => AiAssistantMessage::TYPE_ASSISTANT,
            'text' => $assistantText,
        ]);

        if (! $simulateAssistant) {
            $totalCount = AiAssistantMessage::query()->where('user_id', $user->id)->count();
            $interval = (int) config('ai_assistant.limits.summary_interval', AiAssistantLimits::SUMMARY_INTERVAL);

            if ($totalCount > 0 && $interval > 0 && $totalCount % $interval === 0) {
                $this->summarizeAiAssistantChatAction->execute($user);
            }
        }

        return [
            'user' => $userMessage->fresh(),
            'assistant' => $assistantMessage->fresh(),
        ];
    }

    private function randomDebugReply(): string
    {
        $pool = config('ai_assistant.debug_reply.mock_replies', []);
        if (! is_array($pool) || $pool === []) {
            return (string) config('relationship_coach.fallback_reply');
        }

        $pool = array_values(array_filter($pool, static fn ($line): bool => is_string($line) && $line !== ''));

        return $pool === [] ? (string) config('relationship_coach.fallback_reply') : Arr::random($pool);
    }
}
