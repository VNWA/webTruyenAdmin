<?php

namespace Database\Factories;

use App\Models\Nation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NationFactory extends Factory
{
    protected $model = Nation::class;

    public function definition()
    {
        $name = $this->faker->country();
        return [
            'status' => 1,
            'ord' => $this->faker->numberBetween(1, 100),
            'name' => $name,
            'slug' => Str::slug($name),
            'meta_image' => 'https://via.placeholder.com/300',
            'meta_title' => "Nation: $name",
            'meta_desc' => "This is the description for the nation $name.",
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
