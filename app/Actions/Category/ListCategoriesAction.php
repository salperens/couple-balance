<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListCategoriesAction
{
    /**
     * @return Collection<int, Category>
     */
    public function execute(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }
}
