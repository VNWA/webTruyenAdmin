<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'is_end' =>$this->faker->randomElement([0, 1]),
            'status' => $this->faker->randomElement([0, 1]),
            'highlight' => $this->faker->randomElement([0, 1]),
            'url_avatar' => $this->faker->imageUrl(300, 300, 'animals', true, 'avatar'),
            'full_name' => $this->faker->optional()->name(),
            'name' => $this->faker->word(),
            'slug' => Str::slug($this->faker->unique()->words(3, true)),
            'desc' => $this->faker->optional()->paragraphs(3, true),
            'views' => $this->faker->numberBetween(0, 10000),
            'meta_image' => $this->faker->optional()->imageUrl(300, 300, 'business', true, 'meta'),
            'meta_title' => $this->faker->optional()->sentence(),
            'meta_desc' => $this->faker->optional()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
