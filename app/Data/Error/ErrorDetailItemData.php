<?php

declare(strict_types=1);

namespace App\Data\Error;

use Spatie\LaravelData\Data;

final class ErrorDetailItemData extends Data
{
    public function __construct(
        public readonly string $field,
        public readonly string $message,
    )
    {
    }
}
