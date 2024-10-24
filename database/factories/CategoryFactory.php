<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'status' => $this->faker->numberBetween(0, 1),
            'ord' => $this->faker->numberBetween(0, 100),
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'meta_image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence(),
            'meta_desc' => $this->faker->paragraph(),
        ];
    }
}
