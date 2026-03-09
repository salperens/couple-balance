<?php

namespace App\Http\Responses\Api\V1\Category;

use App\Data\Category\CategoryData;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

final class CategoryResponse
{
    /**
     * @param  Collection<int, Category>  $categories
     */
    public static function list(Collection $categories): JsonResponse
    {
        $data = $categories->map(fn (Category $category): array => CategoryData::from($category)->toArray());

        return response()->json([
            'data' => $data->values()->all(),
        ], Response::HTTP_OK);
    }
}
