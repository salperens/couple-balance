<?php

declare(strict_types=1);

namespace App\Data\Comment;

use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

final class CommentResourceData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $body,
        public readonly bool $isAnonymous,
        public readonly string $authorName,
        public readonly ?int $parentId,
        public readonly CarbonInterface $createdAt,
        public readonly CarbonInterface $updatedAt,
    ) {
    }
}
