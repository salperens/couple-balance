<?php

declare(strict_types=1);

namespace App\Data\Error;

use Spatie\LaravelData\Data;

final class ErrorResponseData extends Data
{
    public function __construct(
        public readonly ErrorDetailData $error,
        public readonly MetaData        $meta,
    )
    {
    }
}
