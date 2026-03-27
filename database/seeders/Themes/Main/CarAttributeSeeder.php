<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Models\CarAmenity;
use Botble\CarRentals\Models\CarAmenityCategory;
use Botble\CarRentals\Models\CarFuel;
use Botble\CarRentals\Models\CarTransmission;
use Botble\CarRentals\Models\CarType;

class CarAttributeSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->runCarType();
        $this->runCarTransmission();
        $this->runCarFuel();
        $this->runCarAmenityCategory();
        $this->runCarAmenity();
    }

    public function runCarType(): void
    {
        CarType::query()->truncate();

        $data = [
            [
                'name' => 'SUV',
                'icon' => 'ti ti-car-suv',
            ],
            [
                'name' => 'Hatchback',
                'icon' => 'ti ti-car',
            ],
            [
                'name' => 'Sedan',
                'icon' => 'ti ti-car',
            ],
            [
                'name' => 'Crossover',
                'icon' => 'ti ti-car-4wd',
            ],
            [
                'name' => 'Minivan',
                'icon' => 'ti ti-caravan',
            ],
            [
                'name' => 'Coupe',
                'icon' => 'ti ti-car',
            ],
            [
                'name' => 'Sport Cars',
                'icon' => 'ti ti-car-turbine',
            ],
            [
                'name' => 'Pickup Truck',
                'icon' => 'ti ti-truck',
            ],
        ];

        foreach ($data as $index => $item) {
            CarType::query()->create([
                ...$item,
                'image' => $this->filePath(sprintf('cars/car-%d.jpg', $index + 1)),
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }
    }

    public function runCarTransmission(): void
    {
        CarTransmission::query()->truncate();

        $data = [
            [
                'name' => 'Automatic',
                'icon' => $this->filePath('icons/car-transmission-auto.png'),
            ],
            [
                'name' => 'Manual',
                'icon' => $this->filePath('icons/car-transmission-manual.png'),
            ],
        ];

        foreach ($data as $item) {
            CarTransmission::query()->create([...$item, 'status' => BaseStatusEnum::PUBLISHED]);
        }
    }

    public function runCarFuel(): void
    {
        CarFuel::query()->truncate();

        $data = [
            [
                'name' => 'Gasoline',
                'icon' => $this->filePath('icons/car-diesel.png'),
            ],
            [
                'name' => 'Diesel',
                'icon' => $this->filePath('icons/car-diesel.png'),
            ],
            [
                'name' => 'Electric',
                'icon' => $this->filePath('icons/car-electricity.png'),
            ],
        ];

        foreach ($data as $item) {
            CarFuel::query()->create([...$item, 'status' => BaseStatusEnum::PUBLISHED]);
        }
    }

    public function runCarAmenityCategory(): void
    {
        CarAmenityCategory::query()->truncate();

        $data = [
            [
                'name' => 'Comfort & Interior',
                'icon' => 'ti ti-armchair',
                'order' => 1,
            ],
            [
                'name' => 'Technology & Infotainment',
                'icon' => 'ti ti-device-tablet',
                'order' => 2,
            ],
            [
                'name' => 'Exterior & Convenience',
                'icon' => 'ti ti-sun',
                'order' => 3,
            ],
            [
                'name' => 'Safety & Driver Assistance',
                'icon' => 'ti ti-shield-check',
                'order' => 4,
            ],
            [
                'name' => 'Performance & Handling',
                'icon' => 'ti ti-steering-wheel',
                'order' => 5,
            ],
            [
                'name' => 'Basic Features',
                'icon' => 'ti ti-car',
                'order' => 6,
            ],
        ];

        foreach ($data as $item) {
            CarAmenityCategory::query()->create([...$item, 'status' => BaseStatusEnum::PUBLISHED]);
        }
    }

    public function runCarAmenity(): void
    {
        CarAmenity::query()->truncate();

        $data = [
            [
                'name' => 'Leather upholstery',
                'icon' => 'ti ti-armchair',
            ],
            [
                'name' => 'Heated seats',
                'icon' => 'ti ti-flame',
            ],
            [
                'name' => 'Ventilated seats',
                'icon' => 'ti ti-air-conditioning',
            ],
            [
                'name' => 'Memory seats',
                'icon' => 'ti ti-device-floppy',
            ],
            [
                'name' => 'Massage seats',
                'icon' => 'ti ti-massage',
            ],
            [
                'name' => 'Premium sound system',
                'icon' => 'ti ti-volume',
            ],
            [
                'name' => 'Wireless charging',
                'icon' => 'ti ti-battery-charging',
            ],
            [
                'name' => 'Dual-zone climate control',
                'icon' => 'ti ti-air-conditioning',
            ],
            [
                'name' => 'Ambient lighting',
                'icon' => 'ti ti-bulb',
            ],
            [
                'name' => 'Touchscreen display',
                'icon' => 'ti ti-device-tablet',
            ],
            [
                'name' => 'Apple CarPlay',
                'icon' => 'ti ti-brand-apple',
            ],
            [
                'name' => 'Android Auto',
                'icon' => 'ti ti-brand-android',
            ],
            [
                'name' => 'Bluetooth connectivity',
                'icon' => 'ti ti-bluetooth',
            ],
            [
                'name' => 'USB ports',
                'icon' => 'ti ti-usb',
            ],
            [
                'name' => 'Navigation system',
                'icon' => 'ti ti-map-pin',
            ],
            [
                'name' => 'Heads-up display',
                'icon' => 'ti ti-device-desktop',
            ],
            [
                'name' => 'Digital instrument cluster',
                'icon' => 'ti ti-dashboard',
            ],
            [
                'name' => 'Voice control',
                'icon' => 'ti ti-microphone',
            ],

            [
                'name' => 'Sunroof/Moonroof',
                'icon' => 'ti ti-sun',
            ],
            [
                'name' => 'Panoramic roof',
                'icon' => 'ti ti-sun-high',
            ],
            [
                'name' => 'Power tailgate',
                'icon' => 'ti ti-door',
            ],
            [
                'name' => 'Keyless entry',
                'icon' => 'ti ti-key',
            ],
            [
                'name' => 'Push-button start',
                'icon' => 'ti ti-power',
            ],
            [
                'name' => 'Remote start',
                'icon' => 'ti ti-remote',
            ],
            [
                'name' => 'Heated mirrors',
                'icon' => 'ti ti-flame',
            ],
            [
                'name' => 'Rain-sensing wipers',
                'icon' => 'ti ti-droplet',
            ],
            [
                'name' => 'Auto-dimming mirrors',
                'icon' => 'ti ti-brightness-down',
            ],
            [
                'name' => 'Adaptive cruise control',
                'icon' => 'ti ti-steering-wheel',
            ],
            [
                'name' => 'Lane departure warning',
                'icon' => 'ti ti-road-sign',
            ],
            [
                'name' => 'Blind spot monitoring',
                'icon' => 'ti ti-eye',
            ],
            [
                'name' => 'Parking sensors',
                'icon' => 'ti ti-radar',
            ],
            [
                'name' => 'Backup camera',
                'icon' => 'ti ti-camera',
            ],
            [
                'name' => '360-degree camera',
                'icon' => 'ti ti-360-view',
            ],
            [
                'name' => 'Automatic emergency braking',
                'icon' => 'ti ti-shield-check',
            ],
            [
                'name' => 'Cross-traffic alert',
                'icon' => 'ti ti-alert-triangle',
            ],
            [
                'name' => 'Driver attention monitoring',
                'icon' => 'ti ti-eye-check',
            ],
            [
                'name' => 'All-wheel drive',
                'icon' => 'ti ti-car-4wd',
            ],
            [
                'name' => 'Sport mode',
                'icon' => 'ti ti-flag',
            ],
            [
                'name' => 'Paddle shifters',
                'icon' => 'ti ti-steering-wheel',
            ],
            [
                'name' => 'Adaptive suspension',
                'icon' => 'ti ti-adjustments',
            ],
            [
                'name' => 'Electronic stability control',
                'icon' => 'ti ti-shield',
            ],
            [
                'name' => 'Traction control',
                'icon' => 'ti ti-wheel',
            ],
            [
                'name' => 'Hill start assist',
                'icon' => 'ti ti-mountain',
            ],
            [
                'name' => 'Downhill assist control',
                'icon' => 'ti ti-trending-down',
            ],
            [
                'name' => 'Air conditioning',
                'icon' => 'ti ti-air-conditioning',
            ],
            [
                'name' => 'Power windows',
                'icon' => 'ti ti-window',
            ],
            [
                'name' => 'Central locking',
                'icon' => 'ti ti-lock',
            ],
        ];

        foreach ($data as $item) {
            CarAmenity::query()->create([...$item, 'status' => BaseStatusEnum::PUBLISHED]);
        }
    }
}
