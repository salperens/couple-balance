<?php

namespace App\Http\Requests\Api\V1\Comment;

use App\Data\Comment\CreateCommentData;
use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'body' => ['required', 'string', 'max:10000'],
            'is_anonymous' => ['required', 'boolean'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:'.(new Comment)->getTable().',id',
                function (string $attribute, mixed $value, \Closure $fail) use ($post): void {
                    if ($value === null) {
                        return;
                    }
                    $comment = Comment::query()->where('id', $value)->where('post_id', $post->id)->first();
                    if ($comment === null) {
                        $fail('parent_id bu posta ait bir yorum olmalıdır.');
                    }
                },
            ],
        ];
    }

    public function toData(): CreateCommentData
    {
        return CreateCommentData::from($this->validated());
    }
}
