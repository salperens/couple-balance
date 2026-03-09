<?php

namespace App\Http\Responses\Api\V1\Post;

use App\Data\Post\PostLikeStateData;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class PostLikeResponse
{
    public static function toggled(PostLikeStateData $state): JsonResponse
    {
        return response()->json(
            [
                'liked'       => $state->liked,
                'likes_count' => $state->likesCount,
            ],
            Response::HTTP_OK,
        );
    }
}

