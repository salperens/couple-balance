<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Genel', 'slug' => 'genel'],
            ['name' => 'İlişki', 'slug' => 'iliski'],
            ['name' => 'Finans', 'slug' => 'finans'],
        ];

        foreach ($categories as $category) {
            Category::query()->firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
