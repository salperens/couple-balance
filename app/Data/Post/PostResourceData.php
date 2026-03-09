<?php

declare(strict_types=1);

namespace App\Data\Post;

use App\Data\Category\CategoryData;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;

final class PostResourceData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $body,
        public readonly bool $isAnonymous,
        public readonly string $authorName,
        /** @var list<CategoryData> */
        public readonly array $categories,
        public readonly CarbonInterface $createdAt,
        public readonly CarbonInterface $updatedAt,
    ) {
    }
}
