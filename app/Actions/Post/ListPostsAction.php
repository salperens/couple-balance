<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;

final readonly class ListPostsAction
{
    public function __construct(
        private Request $request,
    ) {
    }

    public function execute(): Paginator
    {
        $query = Post::query()
            ->with(['user', 'categories'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at');

        $categoryIds = $this->request->input('category_ids');
        if (is_array($categoryIds) && $categoryIds !== []) {
            $query->whereHas('categories', function ($q) use ($categoryIds): void {
                $q->whereIn('categories.id', array_map('intval', $categoryIds));
            });
        }

        return $query->simplePaginate(
            min((int) $this->request->input('per_page', 15), 50)
        );
    }
}
