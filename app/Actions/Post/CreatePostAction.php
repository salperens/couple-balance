<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Data\Post\CreatePostData;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreatePostAction
{
    public function execute(User $user, CreatePostData $data): Post
    {
        return DB::transaction(function () use ($user, $data): Post {
            $slugBase = Str::slug($data->title);
            $slug = $this->generateUniqueSlug($slugBase);

            $post = $user->posts()->create([
                'title' => $data->title,
                'slug' => $slug,
                'body' => $data->body,
                'is_anonymous' => $data->isAnonymous,
            ]);

            if ($data->categoryIds !== []) {
                $post->categories()->attach($data->categoryIds);
            }

            return $post->load('categories');
        });
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $original = $slug;
        $counter = 1;

        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
