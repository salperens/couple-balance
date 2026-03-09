<?php

declare(strict_types=1);

namespace App\Actions\Comment;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;

final readonly class ListPostCommentsAction
{
    public function __construct(
        private Request $request,
    ) {
    }

    public function execute(Post $post): Paginator
    {
        return $post->comments()
            ->with(['user'])
            ->orderBy('created_at')
            ->simplePaginate(
                min((int) $this->request->input('per_page', 15), 50)
            );
    }
}
