<?php

namespace App\Http\Requests\Api\V1\Post;

use App\Data\Post\CreatePostData;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        return [
            'title'          => ['required', 'string', 'max:255'],
            'body'           => ['required', 'string', 'max:65535'],
            'is_anonymous'   => ['required', 'boolean'],
            'category_ids'   => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:' . (new Category)->getTable() . ',id'],
        ];
    }

    public function toData(): CreatePostData
    {
        return CreatePostData::from($this->validated());
    }
}
