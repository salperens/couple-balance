<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Models\AiAssistantMessage;
use App\Models\User;
use App\Support\AiAssistant\AiAssistantLimits;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Messages\MessageRole;
use Laravel\Ai\Promptable;
use Stringable;

#[Temperature(AiAssistantLimits::COACH_TEMPERATURE)]
#[MaxTokens(AiAssistantLimits::COACH_MAX_TOKENS)]
final class RelationshipCoachAgent implements Agent, Conversational
{
    use Promptable;

    public function __construct(
        private readonly User $user,
        private readonly int $excludeMessageId,
        private readonly ?string $rollingSummary,
    ) {}

    public function instructions(): Stringable|string
    {
        $base = (string) config('relationship_coach.base_instructions');

        if ($this->rollingSummary !== null && $this->rollingSummary !== '') {
            $heading = (string) config('relationship_coach.rolling_summary_section_heading');

            return $base."\n\n".$heading.$this->rollingSummary;
        }

        return $base;
    }

    /**
     * @return list<Message>
     */
    public function messages(): iterable
    {
        $contextSize = (int) config('ai_assistant.limits.context_messages', AiAssistantLimits::CONTEXT_MESSAGES);

        $rows = AiAssistantMessage::query()
            ->where('user_id', $this->user->id)
            ->where('id', '<', $this->excludeMessageId)
            ->orderByDesc('id')
            ->limit($contextSize)
            ->get()
            ->sortBy('id')
            ->values();

        return $rows->map(function (AiAssistantMessage $m): Message {
            $role = $m->type === AiAssistantMessage::TYPE_USER
                ? MessageRole::User
                : MessageRole::Assistant;

            return new Message($role, $m->text);
        })->all();
    }

    public function provider(): string
    {
        return (string) config('relationship_coach.provider', config('ai.default', 'openai'));
    }

    public function model(): string
    {
        $model = config('relationship_coach.model');

        return is_string($model) && $model !== ''
            ? $model
            : (string) config('relationship_coach.default_model');
    }
}
