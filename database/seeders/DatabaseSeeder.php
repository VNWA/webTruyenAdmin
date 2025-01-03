<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CompanySeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            EpisodeSeeder::class,
            ServerSeeder::class,
            TypeSeeder::class,
            AppearanceOneSeeder::class,
        ]);
    }
}
