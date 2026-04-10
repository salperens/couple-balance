<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\AiAssistant;

use App\Support\AiAssistant\AiAssistantLimits;
use App\Support\AiAssistantMessageCursor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ListAiAssistantMessagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $min = (int) config('ai_assistant.limits.min_page_size', AiAssistantLimits::MIN_PAGE_SIZE);
        $max = (int) config('ai_assistant.limits.max_page_size', AiAssistantLimits::MAX_PAGE_SIZE);

        return [
            'limit' => 'sometimes|integer|min:'.$min.'|max:'.$max,
            'cursor' => 'sometimes|nullable|string',
        ];
    }

    public function limitValue(): int
    {
        $default = (int) config('ai_assistant.limits.default_page_size', AiAssistantLimits::DEFAULT_PAGE_SIZE);
        $min = (int) config('ai_assistant.limits.min_page_size', AiAssistantLimits::MIN_PAGE_SIZE);
        $max = (int) config('ai_assistant.limits.max_page_size', AiAssistantLimits::MAX_PAGE_SIZE);

        $v = (int) $this->input('limit', $default);

        return $v >= $min && $v <= $max ? $v : $default;
    }

    public function beforeMessageId(): ?int
    {
        $cursor = $this->input('cursor');
        if ($cursor === null || $cursor === '') {
            return null;
        }

        try {
            return AiAssistantMessageCursor::decode($cursor)['lastMessageId'];
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'cursor' => [(string) config('ai_assistant.validation.invalid_cursor')],
            ]);
        }
    }
}
