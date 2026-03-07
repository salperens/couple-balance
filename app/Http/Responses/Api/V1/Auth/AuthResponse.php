<?php

namespace App\Http\Responses\Api\V1\Auth;

use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\NewAccessToken;
use Symfony\Component\HttpFoundation\Response;

final class AuthResponse
{
    public static function success(NewAccessToken $tokenResult, int $status = Response::HTTP_OK): JsonResponse
    {
        $user = $tokenResult->accessToken->tokenable;

        return response()->json(
            [
                'token'      => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ],
            $status,
        );
    }

    public static function unauthorized(string $message = 'Geçersiz e-posta veya şifre.'): JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
            ],
            Response::HTTP_UNAUTHORIZED,
        );
    }
}
