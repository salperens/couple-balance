<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Category\ListCategoriesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesAction $listCategoriesAction,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = $this->listCategoriesAction->execute();

        return CategoryResource::collection($categories);
    }
}
