<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Responses\Api\V1\Auth\AuthResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterUserAction $registerUserAction,
        private readonly LoginUserAction $loginUserAction,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $tokenResult = $this->registerUserAction->execute($request->toData());

        return AuthResponse::success($tokenResult, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $tokenResult = $this->loginUserAction->execute($request->toData());

        if (! $tokenResult) {
            return AuthResponse::unauthorized();
        }

        return AuthResponse::success($tokenResult);
    }
}
