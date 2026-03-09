<?php

namespace App\Http\Responses\Api\V1\Comment;

use App\Data\Comment\CommentResourceData;
use App\Models\Comment;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CommentResponse
{
    public static function list(Paginator $paginator): JsonResponse
    {
        $items = collect($paginator->items())->map(
            fn (Comment $comment): array => self::commentToArray($comment)
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

    public static function created(Comment $comment): JsonResponse
    {
        $comment->load('user');

        return response()->json([
            'data' => self::commentToArray($comment),
        ], Response::HTTP_CREATED);
    }

    /**
     * @return array<string, mixed>
     */
    private static function commentToArray(Comment $comment): array
    {
        $resource = CommentResourceData::from([
            'id' => $comment->id,
            'body' => $comment->body,
            'is_anonymous' => $comment->is_anonymous,
            'author_name' => $comment->is_anonymous ? 'Anonim' : $comment->user->name,
            'parent_id' => $comment->parent_id,
            'created_at' => $comment->created_at,
            'updated_at' => $comment->updated_at,
        ]);

        return $resource->toArray();
    }
}
