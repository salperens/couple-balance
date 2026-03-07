<?php

namespace App\Actions\Auth;

use App\Data\Auth\LoginData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

final readonly class LoginUserAction
{
    public function execute(LoginData $data): ?NewAccessToken
    {
        $user = User::query()->where('email', $data->email)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            return null;
        }

        $device = (new FindOrCreateDeviceAction)->execute($user->id, $data->device);

        $tokenResult = $user->createToken('auth');
        $tokenResult->accessToken->update(['device_id' => $device->id]);

        return $tokenResult;
    }
}
