<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Data\Post\PostLikeStateData;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class TogglePostLikeAction
{
    public function execute(Post $post, User $user): PostLikeStateData
    {
        return DB::transaction(function () use ($post, $user): PostLikeStateData {
            $existing = $post->likes()
                ->where('user_id', $user->id)
                ->first();

            if ($existing !== null) {
                $existing->delete();
                $liked = false;
            } else {
                $post->likes()->create([
                    'user_id' => $user->id,
                ]);
                $liked = true;
            }

            $likesCount = $post->likes()->count();

            return new PostLikeStateData(
                liked: $liked,
                likesCount: $likesCount,
            );
        });
    }
}

