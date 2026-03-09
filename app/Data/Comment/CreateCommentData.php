<?php

declare(strict_types=1);

namespace App\Data\Comment;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateCommentData extends Data
{
    public function __construct(
        public readonly string $body,
        public readonly bool $isAnonymous,
        public readonly ?int $parentId = null,
    ) {
    }
}
