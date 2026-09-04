<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Everyday Elegance',
            'Soft Glam',
            'Bold & Chic',
            'Luxury Edition',
            'Bridal Collection',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
