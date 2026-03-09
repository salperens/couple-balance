<?php

declare(strict_types=1);

namespace App\Actions\Comment;

use App\Data\Comment\CreateCommentData;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class CreateCommentAction
{
    public function execute(Post $post, User $user, CreateCommentData $data): Comment
    {
        return DB::transaction(function () use ($post, $user, $data): Comment {
            return $post->allComments()->create([
                'user_id' => $user->id,
                'parent_id' => $data->parentId,
                'body' => $data->body,
                'is_anonymous' => $data->isAnonymous,
            ]);
        });
    }
}
