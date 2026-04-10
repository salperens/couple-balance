<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Support\AiAssistant\AiAssistantLimits;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

#[UseCheapestModel]
#[Temperature(AiAssistantLimits::SUMMARIZER_TEMPERATURE)]
#[MaxTokens(AiAssistantLimits::SUMMARIZER_MAX_TOKENS)]
final class ChatSummarizerAgent implements Agent
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return (string) config('relationship_coach.summarizer_instructions');
    }
}
