<?php

declare(strict_types=1);

namespace App\Support\AiAssistant;

final class AiAssistantLimits
{
    public const DEFAULT_PAGE_SIZE = 20;

    public const MIN_PAGE_SIZE = 1;

    public const MAX_PAGE_SIZE = 50;

    public const CONTEXT_MESSAGES = 10;

    public const SUMMARY_INTERVAL = 10;

    public const COACH_MAX_TOKENS = 1024;

    public const COACH_TEMPERATURE = 0.45;

    public const SUMMARIZER_MAX_TOKENS = 550;

    public const SUMMARIZER_TEMPERATURE = 0.25;

    public const MAX_MESSAGE_LENGTH = 10_000;
}
