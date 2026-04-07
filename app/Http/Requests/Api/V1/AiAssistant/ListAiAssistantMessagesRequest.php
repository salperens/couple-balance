<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\AiAssistant;

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
        return [
            'limit'  => 'sometimes|integer|min:1|max:50',
            'cursor' => 'sometimes|nullable|string',
        ];
    }

    public function limitValue(): int
    {
        $v = (int)$this->input('limit', 20);

        return $v >= 1 && $v <= 50 ? $v : 20;
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
                'cursor' => ['Geçersiz cursor.'],
            ]);
        }
    }
}
