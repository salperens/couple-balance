<?php

namespace App\Actions\Auth;

use App\Data\Auth\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\NewAccessToken;

final readonly class RegisterUserAction
{
    public function execute(RegisterData $data): NewAccessToken
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $device = (new FindOrCreateDeviceAction)->execute($user->id, $data->device);

            $tokenResult = $user->createToken('auth');
            $tokenResult->accessToken->update(['device_id' => $device->id]);

            return $tokenResult;
        });
    }
}
