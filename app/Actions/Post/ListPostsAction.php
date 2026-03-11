<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Data\Post\ListPostRequestData;
use App\Models\Post;
use App\Models\User;
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

    public function execute(?ListPostRequestData $requestData = null, ?User $user = null): Paginator
    {
        $userId = $user?->id;

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
            ->withExists([
                'likes as is_liked' => fn ($q) => $q->where('user_id', $userId)
            ])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at');

        return $query->simplePaginate(
            min((int)$this->request->input('per_page', 15), 50)
        );
    }
}
