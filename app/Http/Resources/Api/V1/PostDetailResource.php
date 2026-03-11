<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;

/** @mixin Post */
class PostDetailResource extends PostResource
{
    protected function getBody(): ?string
    {
        return $this->body;
    }
}

