<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;
use App\Models\Category;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $technology = Category::where('nama_kriteria', 'Teknologi')->first();
        if ($technology) {
            $this->createSubcategory('Pemrograman', $technology->id_kriteria, 'Subkategori pemrograman komputer basic ke advance');
            $this->createSubcategory('Jaringan', $technology->id_kriteria, 'Subkategori jaringan nirkabel dan berbayar');
        }

        $business = Category::where('nama_kriteria', 'Bisnis')->first();
        if ($business) {
            $this->createSubcategory('Marketing', $business->id_kriteria, 'Subkategori edukasi marketing untuk B2B Sales');
            $this->createSubcategory('Keuangan', $business->id_kriteria, 'Subkategori keuangan perbankan atau desentralisasi');
        }
        
        $design = Category::where('nama_kriteria', 'Desain')->first();
        if ($design) {
            $this->createSubcategory('UI/UX', $design->id_kriteria, 'Subkategori perancangan UI/UX Desain Sistem');
            $this->createSubcategory('Ilustrasi', $design->id_kriteria, 'Subkategori perancangan Vector Ilustrasi');
        }
    }

    private function createSubcategory($name, $categoryId, $description)
    {
        $existing = Subcategory::where('nama_subkriteria', $name)->where('category_id', $categoryId)->first();
        if (!$existing) {
            Subcategory::create(['name' => $name, 'category_id' => $categoryId, 'description' => $description]);
        }
    }
}
