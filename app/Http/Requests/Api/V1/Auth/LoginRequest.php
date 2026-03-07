<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Data\Auth\LoginData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'              => ['required', 'string', 'email'],
            'password'           => ['required', 'string'],
            'device'             => ['required', 'array'],
            'device.device_id'   => ['required', 'string', 'max:255'],
            'device.os'          => ['required', 'string', 'in:ios,android,web'],
            'device.model'       => ['nullable', 'string', 'max:255'],
            'device.app_version' => ['nullable', 'string', 'max:50'],
            'device.push_token'  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toData(): LoginData
    {
        return LoginData::from($this->validated());
    }
}
