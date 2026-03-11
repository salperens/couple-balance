<?php

namespace App\Exceptions\Handlers\Contracts;

use App\Data\Error\ErrorResponseData;
use Illuminate\Http\Request;
use Throwable;

interface ExceptionHandler
{
    public function handle(Throwable $exception, Request $request): ?ErrorResponseData;

    public function shouldHandle(Throwable $exception, Request $request): bool;

    public function getStatusCode(): int;
}
