<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        User::truncate();

        User::create([
            'name' => 'DEV',
            'email' => 'nhatnguyen.dev.fullstack@gmail.com',
            'password' => Hash::make('dev@123'),
        ]);
        User::create([
            'name' => 'ADMin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@123'),
        ]);
    }
}
