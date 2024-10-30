<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy',
            'Horror', 'Isekai', 'Martial Arts', 'Mystery', 'Romance',
            'Sci-Fi', 'Slice of Life', 'Sports', 'Supernatural', 'Thriller',
            'Historical', 'Mecha', 'Psychological', 'Seinen', 'Shoujo',
            'Shounen', 'Josei', 'Yaoi', 'Yuri', 'Ecchi'
        ];

        foreach ($types as $index => $type) {
            Type::create([
                'status' => 1,
                'highlight' => rand(0, 1),
                'ord' => $index + 1,
                'name' => $type,
                'slug' => Str::slug($type),
                'meta_image' => 'https://via.placeholder.com/300',
                'meta_title' => "Genre: $type",
                'meta_desc' => "This is a comic genre classified as $type.",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
