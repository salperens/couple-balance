<?php

declare(strict_types=1);

namespace App\Data\Post;

use Spatie\LaravelData\Data;

final class PostLikeStateData extends Data
{
    public function __construct(
        public readonly bool $liked,
        public readonly int $likesCount,
    ) {
    }
}

