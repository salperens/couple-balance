<?php

declare(strict_types=1);

namespace App\Data\Error;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class MetaData extends Data
{
    public function __construct(
        public readonly string                     $requestId,
        public readonly \Illuminate\Support\Carbon $timestamp,
    )
    {
    }
}
