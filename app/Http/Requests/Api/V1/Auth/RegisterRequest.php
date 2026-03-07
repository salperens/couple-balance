<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Data\Auth\RegisterData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'device' => ['required', 'array'],
            'device.device_id' => ['required', 'string', 'max:255'],
            'device.os' => ['required', 'string', 'in:ios,android,web'],
            'device.model' => ['nullable', 'string', 'max:255'],
            'device.app_version' => ['nullable', 'string', 'max:50'],
            'device.push_token' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toData(): RegisterData
    {
        return RegisterData::from($this->validated());
    }
}
