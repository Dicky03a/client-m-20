<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Teknologi', 'description' => 'Kategori tentang teknologi rancang bangun'],
            ['name' => 'Bisnis', 'description' => 'Kategori tentang bisnis dan ekonomi makro'],
            ['name' => 'Desain', 'description' => 'Kategori pengembangan grafis desain antar muka'],
        ];

        foreach ($categories as $category) {
            $existing = Category::where('nama_kriteria', $category['name'])->first();
            if (!$existing) {
                Category::create($category);
            }
        }
    }
}
