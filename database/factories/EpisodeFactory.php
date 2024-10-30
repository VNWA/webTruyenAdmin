<?php

namespace Database\Factories;

use App\Models\Episode;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
        ];
    }
}
