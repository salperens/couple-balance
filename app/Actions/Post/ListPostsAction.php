<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Data\Post\ListPostRequestData;
use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final readonly class ListPostsAction
{
    public function __construct(
        private Request $request,
    )
    {
    }

    public function execute(?ListPostRequestData $requestData = null): Paginator
    {

        $query = Post::query()
            ->with(['user', 'categories'])
            ->when(
                !is_null($requestData?->categoryId),
                function (Builder $query) use ($requestData): Builder {
                    return $query->whereHas(
                        'categories',
                        static fn(Builder $builder) => $builder->where('categories.id', $requestData->categoryId)
                    );
                }
            )
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at');

        return $query->simplePaginate(
            min((int)$this->request->input('per_page', 15), 50)
        );
    }
}
