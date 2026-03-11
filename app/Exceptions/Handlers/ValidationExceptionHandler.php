<?php

declare(strict_types=1);

namespace App\Exceptions\Handlers;

use App\Data\Error\ErrorDetailData;
use App\Data\Error\ErrorDetailItemData;
use App\Data\Error\ErrorResponseData;
use App\Data\Error\MetaData;
use App\Exceptions\Handlers\Concerns\HandlesApiRequests;
use App\Exceptions\Handlers\Contracts\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\DataCollection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ValidationExceptionHandler implements ExceptionHandler
{
    use HandlesApiRequests;

    private int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function handle(Throwable $exception, Request $request): ?ErrorResponseData
    {
        if (!$this->shouldHandle($exception, $request)) {
            return null;
        }

        $errorDetailItems = [];

        foreach ($exception->errors() as $key => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $errorDetailItems[] = [
                    'field' => $key,
                    'message' => $errorMessage,
                ];
            }
        }

        return new ErrorResponseData(
            new ErrorDetailData(
                'VALIDATION_ERROR',
                'The given data was invalid.',
                new DataCollection(ErrorDetailItemData::class, $errorDetailItems),
            ),
            new MetaData(
                Str::random(32),
                Carbon::now()
            )
        );
    }

    public function shouldHandle(Throwable $exception, Request $request): bool
    {
        return $exception instanceof ValidationException && $this->isApiRequest($request);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
