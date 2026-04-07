<?php

namespace App\Exceptions\Handlers;

use App\Data\Error\ErrorDetailData;
use App\Data\Error\ErrorResponseData;
use App\Data\Error\MetaData;
use App\Exceptions\Handlers\Concerns\HandlesApiRequests;
use App\Exceptions\Handlers\Contracts\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DefaultExceptionHandler implements ExceptionHandler
{
    use HandlesApiRequests;

    private const DEFAULT_EXCEPTION_MESSAGE = 'An error occurred';

    private int $statusCode = Response::HTTP_BAD_REQUEST;

    public function handle(Throwable $exception, Request $request): ?ErrorResponseData
    {
        if (!$this->shouldHandle($exception, $request)) {
            return null;
        }

        $this->setStatusCode($exception);
        $message = $this->getMessage($exception);

        return new ErrorResponseData(
            new ErrorDetailData(
                'GENERAL_ERROR',
                $message,
            ),
            new MetaData(
                Str::random(32),
                Carbon::now()
            ),
        );

    }

    public function shouldHandle(Throwable $exception, Request $request): bool
    {
        return $this->isApiRequest($request);
    }

    private function setStatusCode(Throwable $exception): void
    {
        if (method_exists($exception, 'getStatusCode')) {
            $this->statusCode = $exception->getStatusCode();
        }

        $exceptionCode = $this->normalizeExceptionHttpCode($exception->getCode());
        if ($exceptionCode !== null) {
            $this->statusCode = $exceptionCode;
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Exception::getCode() is not always int across extensions; only map numeric 4xx–5xx to HTTP status.
     */
    private function normalizeExceptionHttpCode(mixed $code): ?int
    {
        if (is_string($code)) {
            if ($code === '' || !ctype_digit($code)) {
                return null;
            }
            $code = (int) $code;
        } elseif (!is_int($code)) {
            return null;
        }

        return $this->isCodeValid($code) ? $code : null;
    }

    private function isCodeValid(int $code): bool
    {
        return $code >= 400 && $code < 600;
    }

    private function getMessage(Throwable $exception): string
    {
        return $exception->getMessage() ?: self::DEFAULT_EXCEPTION_MESSAGE;
    }
}
