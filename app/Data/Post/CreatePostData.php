<?php

declare(strict_types=1);

namespace App\Data\Post;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreatePostData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly bool $isAnonymous,
        /** @var array<int, int> */
        public readonly array $categoryIds,
    ) {
    }
}
