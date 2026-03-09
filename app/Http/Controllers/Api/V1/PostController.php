<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Post\CreatePostAction;
use App\Actions\Post\ListPostsAction;
use App\Actions\Post\TogglePostLikeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Post\StorePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

final class PostController extends Controller
{
    public function __construct(
        private readonly ListPostsAction     $listPostsAction,
        private readonly CreatePostAction    $createPostAction,
        private readonly TogglePostLikeAction $togglePostLikeAction,
    ) {
    }

    public function index()
    {
        $paginator = $this->listPostsAction->execute();

        return PostResource::collection($paginator);
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->createPostAction->execute($request->user(), $request->toData());

        return (new PostResource($post->load(['user', 'categories'])))
            ->response()
            ->setStatusCode(201);
    }

    public function toggleLike(Post $post): JsonResponse
    {
        $state = $this->togglePostLikeAction->execute($post, request()->user());

        return \App\Http\Responses\Api\V1\Post\PostLikeResponse::toggled($state);
    }
}
