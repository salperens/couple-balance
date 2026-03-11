<?php

declare(strict_types=1);

namespace App\Data\Error;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class ErrorDetailData extends Data
{
    public function __construct(
        public readonly string          $code,
        public readonly string          $message,
        #[DataCollectionOf(ErrorDetailItemData::class)]
        public readonly ?DataCollection $details = null,
    )
    {
    }
}
