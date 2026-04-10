<?php

declare(strict_types=1);

use App\Support\AiAssistant\AiAssistantLimits;

return [

    'limits' => [
        'default_page_size' => (int) env('AI_ASSISTANT_LIMIT_DEFAULT', AiAssistantLimits::DEFAULT_PAGE_SIZE),
        'min_page_size' => (int) env('AI_ASSISTANT_LIMIT_MIN', AiAssistantLimits::MIN_PAGE_SIZE),
        'max_page_size' => (int) env('AI_ASSISTANT_LIMIT_MAX', AiAssistantLimits::MAX_PAGE_SIZE),
        'context_messages' => (int) env('AI_ASSISTANT_CONTEXT_MESSAGES', AiAssistantLimits::CONTEXT_MESSAGES),
        'summary_interval' => (int) env('AI_ASSISTANT_SUMMARY_INTERVAL', AiAssistantLimits::SUMMARY_INTERVAL),
        'max_message_length' => (int) env('AI_ASSISTANT_MAX_MESSAGE_LENGTH', AiAssistantLimits::MAX_MESSAGE_LENGTH),
    ],

    'api' => [
        'messages_path' => env('AI_ASSISTANT_MESSAGES_PATH', '/ai-assistant/messages'),
    ],

    'chat_state' => [
        'default_topic' => env('AI_ASSISTANT_CHAT_TOPIC', 'relationship_wellbeing'),
        'default_emotion' => env('AI_ASSISTANT_CHAT_EMOTION', 'neutral'),
    ],

    'validation' => [
        'invalid_cursor' => 'Geçersiz cursor.',
    ],

    'welcome' => [
        'messages_config_key' => 'relationship_coach.welcome_messages',
    ],

];
