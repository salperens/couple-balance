<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class DeviceData extends Data
{
    public function __construct(
        public readonly string  $deviceId,
        public readonly string  $os,
        public readonly ?string $model = null,
        public readonly ?string $appVersion = null,
        public readonly ?string $pushToken = null,
    )
    {
    }
}
