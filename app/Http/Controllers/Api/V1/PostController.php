<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Post\CreatePostAction;
use App\Actions\Post\ListPostsAction;
use App\Actions\Post\TogglePostLikeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Post\ListPostRequest;
use App\Http\Requests\Api\V1\Post\StorePostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Responses\Api\V1\Post\PostLikeResponse;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class PostController extends Controller
{
    public function __construct(
        private readonly ListPostsAction      $listPostsAction,
        private readonly CreatePostAction     $createPostAction,
        private readonly TogglePostLikeAction $togglePostLikeAction,
    )
    {
    }

    public function index(ListPostRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->listPostsAction->execute($request->toData(), $request->user() ?? null);


        return PostResource::collection($paginator);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->createPostAction->execute($request->user(), $request->toData());

        $post->load(['user', 'categories'])->loadCount(['likes', 'comments']);

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function toggleLike(Post $post): JsonResponse
    {
        $state = $this->togglePostLikeAction->execute($post, request()->user());

        return PostLikeResponse::toggled($state);
    }
}
