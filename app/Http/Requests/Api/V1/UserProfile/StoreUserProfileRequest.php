<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\UserProfile;

use App\Data\UserProfile\UserProfileData;
use App\Enums\GenderEnum;
use App\Enums\OrientationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'age'         => ['integer', 'min:12', 'max:100'],
            'gender'      => [new Enum(GenderEnum::class)],
            'orientation' => [new Enum(OrientationEnum::class)],
        ];
    }

    public function toData(): UserProfileData
    {
        return UserProfileData::from($this->validated());
    }
}
