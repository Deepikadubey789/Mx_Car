<?php

namespace Botble\CarRentals\Database\Seeders;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarAmenityCategory;
use Illuminate\Database\Seeder;

class CarAmenityCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Comfort',
                'icon' => 'ti ti-sofa',
                'order' => 1,
            ],
            [
                'name' => 'Safety',
                'icon' => 'ti ti-shield-check',
                'order' => 2,
            ],
            [
                'name' => 'Seat',
                'icon' => 'ti ti-armchair',
                'order' => 3,
            ],
            [
                'name' => 'Sound System',
                'icon' => 'ti ti-music',
                'order' => 4,
            ],
            [
                'name' => 'Windows',
                'icon' => 'ti ti-window',
                'order' => 5,
            ],
            [
                'name' => 'Others',
                'icon' => 'ti ti-dots',
                'order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            CarAmenityCategory::query()->firstOrCreate(
                ['name' => $category['name']],
                array_merge($category, ['status' => BaseStatusEnum::PUBLISHED])
            );
        }
    }
}
