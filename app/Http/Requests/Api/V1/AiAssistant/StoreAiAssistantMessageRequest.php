<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\AiAssistant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAiAssistantMessageRequest extends FormRequest
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
            'text' => 'required|string|max:10000',
        ];
    }
}
