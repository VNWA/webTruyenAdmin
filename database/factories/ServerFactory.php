<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition()
    {
        return [
            'episode_id' => \App\Models\Episode::factory(),
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl(), $this->faker->imageUrl()]
        ];
    }
}
