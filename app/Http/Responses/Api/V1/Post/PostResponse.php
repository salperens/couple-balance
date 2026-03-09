<?php

namespace App\Http\Responses\Api\V1\Post;

use App\Data\Category\CategoryData;
use App\Data\Post\PostResourceData;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

final class PostResponse
{
    public static function list(Paginator $paginator): JsonResponse
    {
        $items = collect($paginator->items())->map(
            fn (Post $post): array => self::postToArray($post)
        );

        return response()->json([
            'data' => $items,
            'meta' => [
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], Response::HTTP_OK);
    }

    public static function created(Post $post): JsonResponse
    {
        return response()->json([
            'data' => self::postToArray($post->load(['user', 'categories'])),
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array<string, mixed>
     */
    private static function postToArray(Post $post): array
    {
        $resource = PostResourceData::from([
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'body' => $post->body,
            'is_anonymous' => $post->is_anonymous,
            'author_name' => $post->is_anonymous ? 'Anonim' : $post->user->name,
            'categories' => $post->categories->map(fn ($c) => CategoryData::from($c))->all(),
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ]);

        return $resource->toArray();
    }
}
