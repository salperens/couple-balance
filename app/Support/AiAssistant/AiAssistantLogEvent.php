<?php

declare(strict_types=1);

namespace App\Support\AiAssistant;

final class AiAssistantLogEvent
{
    public const COACH_FAILED = 'ai_assistant.coach_failed';

    public const SUMMARIZE_FAILED = 'ai_assistant.summarize_failed';
}
