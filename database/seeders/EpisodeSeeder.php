<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Product::all() as $product) {
            Episode::factory()->count(5)->create(['product_id' => $product->id]);
        }
    }
}
