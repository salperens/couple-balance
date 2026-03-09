<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\ListPostCommentsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Comment\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

final class CommentController extends Controller
{
    public function __construct(
        private readonly ListPostCommentsAction $listPostCommentsAction,
        private readonly CreateCommentAction $createCommentAction,
    ) {
    }

    public function index(Post $post)
    {
        $paginator = $this->listPostCommentsAction->execute($post);

        return CommentResource::collection($paginator);
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->createCommentAction->execute($post, $request->user(), $request->toData());

        return (new CommentResource($comment->load('user')))
            ->response()
            ->setStatusCode(201);
    }
}
