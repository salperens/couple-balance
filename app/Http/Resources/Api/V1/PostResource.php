<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Post */
class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'body'           => $this->getBody(),
            'is_liked'       => $this->is_liked,
            'is_anonymous'   => $this->is_anonymous,
            'author_name'    => $this->is_anonymous ? 'Anonim' : ($this->user->name ?? null),
            'likes_count'    => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'categories'     => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }

    protected function getBody(): ?string
    {
        if (strlen($this->body) < 100) {
            return $this->body;
        }

        return rtrim(substr($this->body, 0, 100)) . '...';
    }
}

