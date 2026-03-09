<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Comment */
class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'is_anonymous' => (bool) $this->is_anonymous,
            'author_name' => $this->is_anonymous ? 'Anonim' : ($this->user->name ?? null),
            'parent_id' => $this->parent_id,
            // Sonsuz seviye nested replies
            'replies' => CommentResource::collection($this->replies),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

