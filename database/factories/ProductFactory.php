<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'id_year' => null,
            'id_nation' => null,
            'status' => $this->faker->numberBetween(0, 1),
            'highlight' => $this->faker->numberBetween(0, 1),
            'is_18' => 1,
            'ord' => $this->faker->numberBetween(0, 100),
            'url_avatar' => $this->faker->imageUrl(),
            'url_bg' => $this->faker->optional()->imageUrl(),
            'full_name' => $this->faker->name(),
            'date' => $this->faker->date(),
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'desc' => $this->faker->paragraph(),
            'meta_image' => $this->faker->optional()->imageUrl(),
            'meta_title' => $this->faker->sentence(),
            'meta_desc' => $this->faker->paragraph(),
        ];
    }
}
