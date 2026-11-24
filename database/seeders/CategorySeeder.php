<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $parents = Category::factory(5)->create();

        Category::factory(5)->create([
            'parent_id' => fn () => $parents->random()->id,
        ]);
    }
}
