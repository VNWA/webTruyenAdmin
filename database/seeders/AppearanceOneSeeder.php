<?php

namespace Database\Seeders;

use App\Models\Appearance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppearanceOneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appearance::truncate();

        Appearance::create([
            'type' => 'banner_ads',
            'value' => [
                'top_home' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ],
                'top_raw_manga' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ]
                ,
                'top_sub_manga' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ]
                ,
                'top_new_manga' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ],
                'product' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ],
                'episode_one' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ],
                'episode_two' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ],
                'episode_three' => [
                    'iframe' => '',
                    'image' => '',
                    'link' => '/',
                    'isImage' => 0
                ]
            ],
        ]);

    }
}
