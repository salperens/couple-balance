<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\UserProfile;


use App\Enums\GenderEnum;
use App\Enums\OrientationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'age'         => ['sometimes', 'integer', 'min:18', 'max:100'],
            'gender'      => ['sometimes', new Enum(GenderEnum::class)],
            'orientation' => ['sometimes', new Enum(OrientationEnum::class)],
        ];
    }
}
