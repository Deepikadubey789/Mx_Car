<?php

namespace Botble\CarRentals\Database\Traits;

use Botble\ACL\Models\User;
use Botble\Base\Facades\MetaBox;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarAmenity;
use Botble\CarRentals\Models\CarColor;
use Botble\CarRentals\Models\CarFuel;
use Botble\CarRentals\Models\CarMake;
use Botble\CarRentals\Models\CarType;
use Botble\CarRentals\Models\Customer;
use Botble\Faq\Models\Faq;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;

trait HasCarSeeder
{
    protected function createCars(array $cars, bool $truncate = true): void
    {
        if ($truncate) {
            Car::query()->truncate();
        }

        $makeIds = CarMake::query()->pluck('id')->all();
        $vehicleTypeIds = CarType::query()->pluck('id')->all();
        $fuelTypeIds = CarFuel::query()->pluck('id')->all();
        $transmissionTypeIds = CarFuel::query()->pluck('id')->all();
        $colorIds = CarColor::query()->pluck('id')->all();
        $vendorIds = Customer::query()->where('is_vendor', 1)->pluck('id')->all();
        $amenityIds = CarAmenity::query()->pluck('id')->all();
        $faqIds = is_plugin_active('faq') ? Faq::query()->pluck('id') : collect();

        // Get cities and states if location plugin is active
        $cityIds = [];
        $stateData = [];
        if (is_plugin_active('location')) {
            $cities = City::query()->with(['state', 'country'])->wherePublished()->get();
            if ($cities->isNotEmpty()) {
                foreach ($cities as $city) {
                    $cityIds[] = [
                        'city_id' => $city->id,
                        'state_id' => $city->state_id,
                        'country_id' => $city->country_id,
                    ];
                }
            } else {
                // If no cities, just get states with their countries
                $states = State::query()->with('country')->wherePublished()->get();
                if ($states->isNotEmpty()) {
                    foreach ($states as $state) {
                        $stateData[] = [
                            'state_id' => $state->id,
                            'country_id' => $state->country_id,
                        ];
                    }
                }
            }
        }

        foreach ($cars as $key => $item) {
            $images = [];

            for ($i = 1; $i <= 2; $i++) {
                $images[] = $this->filePath(sprintf('cars/%s.jpg', rand(1, 105)));
            }

            for ($i = 1; $i <= 4; $i++) {
                $images[] = $this->filePath(sprintf('cars/car-interiors-%s.jpg', rand(1, 8)));
            }

            // Add external booking URL to approximately 1/4 of the cars
            $hasExternalBooking = rand(1, 4) === 1;
            $externalBookingUrl = null;

            if ($hasExternalBooking) {
                $bookingSites = [
                    // Major car rental companies
                    'https://www.hertz.com/rentacar/reservation/vehicles?location=',
                    'https://www.enterprise.com/en/reserve.html?vehicleType=',
                    'https://www.avis.com/en/reserve/vehicles?location=',
                    'https://www.budget.com/en/reservation/vehicles?location=',
                    'https://www.sixt.com/rental-car/usa/vehicle/',
                    'https://www.europcar.com/en-us/car-rental/vehicles/',
                    // Aggregators and online travel agencies
                    'https://www.rentalcars.com/en/search-results/',
                    'https://www.kayak.com/cars/',
                    'https://www.expedia.com/carsearch?vehicle=',
                    'https://www.turo.com/us/en/search?vehicle=',
                ];

                $carSlug = str_replace(' ', '-', strtolower($item['name']));
                $externalBookingUrl = $bookingSites[array_rand($bookingSites)] . $carSlug;
            }

            // Assign location (city, state, and country) to the car
            $locationData = [];
            if (! empty($cityIds)) {
                // If we have cities, randomly assign a city (which includes state and country)
                $location = $cityIds[array_rand($cityIds)];
                $locationData = [
                    'city_id' => $location['city_id'],
                    'state_id' => $location['state_id'],
                    'country_id' => $location['country_id'],
                    'address' => $this->generateRandomAddress(),
                ];
            } elseif (! empty($stateData)) {
                // If we only have states, assign a state with country
                $location = $stateData[array_rand($stateData)];
                $locationData = [
                    'city_id' => null,
                    'state_id' => $location['state_id'],
                    'country_id' => $location['country_id'],
                    'address' => $this->generateRandomAddress(),
                ];
            } else {
                // No location data, still generate an address
                $locationData = [
                    'address' => $this->generateRandomAddress(),
                ];
            }

            // 80% chance of assigning to vendor, 20% to admin
            $assignToVendor = ! empty($vendorIds) && rand(1, 10) <= 8;
            $vendorId = null;

            if ($assignToVendor) {
                $vendorId = $vendorIds[array_rand($vendorIds)];
                $authorId = $vendorId;
                $authorType = Customer::class;
            } else {
                $authorId = 1; // Admin user
                $authorType = User::class;
            }

            /**
             * @var Car $car
             */
            $car = Car::query()->forceCreate(
                [
                    ...$item,
                    ...$locationData,
                    'number_of_seats' => [4, 5, 7, 8][rand(0, 3)],
                    'number_of_doors' => [2, 4, 5][rand(0, 2)],
                    'is_featured' => $key % 2 == 0 ? 1 : 0,
                    'is_used' => rand(0, 3) === 0 ? 1 : 0,
                    'make_id' => $makeIds[rand(0, count($makeIds) - 1)],
                    'vin' => $this->generateVin(),
                    'mileage' => rand(1000, 20000),
                    'rental_rate' => rand(30, 99),
                    'content' => file_get_contents(database_path('seeders/contents/car.html')),
                    'status' => CarStatusEnum::AVAILABLE,
                    'images' => $images,
                    'year' => rand(2010, 2024),
                    'vehicle_type_id' => $vehicleTypeIds[rand(0, count($vehicleTypeIds) - 1)],
                    'fuel_type_id' => $fuelTypeIds[rand(0, count($fuelTypeIds) - 1)],
                    'transmission_id' => $transmissionTypeIds[rand(0, count($transmissionTypeIds) - 1)],
                    'moderation_status' => ModerationStatusEnum::APPROVED,
                    'author_id' => $authorId,
                    'author_type' => $authorType,
                    'vendor_id' => $vendorId,
                    'external_booking_url' => $externalBookingUrl,
                ],
            );

            $car->colors()->sync([$colorIds[rand(0, count($colorIds) - 1)]]);

            // Assign amenities based on car type and price range
            if (! empty($amenityIds)) {
                $assignedAmenities = $this->getAmenitiesForCar($car, $amenityIds);
                $car->amenities()->sync($assignedAmenities);
            }

            if ($faqIds->isNotEmpty()) {
                MetaBox::saveMetaBoxData(
                    $car,
                    'faq_ids',
                    $faqIds->random($faqIds->count() >= 5 ? 5 : 1)->all()
                );
            }
        }
    }

    public function generateVin(): string
    {
        $allowedChars = '0123456789ABCDEFGHJKLMNPRSTUVWXYZ';

        $vin = '';

        for ($i = 0; $i < 3; $i++) {
            $vin .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
        }

        for ($i = 0; $i < 5; $i++) {
            $vin .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
        }

        $vin .= rand(0, 9);

        for ($i = 0; $i < 8; $i++) {
            $vin .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
        }

        return $vin;
    }

    /**
     * Get amenities for a car based on its characteristics
     */
    protected function getAmenitiesForCar(Car $car, array $amenityIds): array
    {
        // Define amenity categories by name patterns
        $basicAmenities = $this->getAmenitiesByNames($amenityIds, [
            'Bluetooth connectivity', 'USB ports', 'Air conditioning', 'Power windows',
            'Central locking', 'Keyless entry', 'Push-button start',
        ]);

        $comfortAmenities = $this->getAmenitiesByNames($amenityIds, [
            'Leather upholstery', 'Heated seats', 'Dual-zone climate control',
            'Premium sound system', 'Sunroof/Moonroof', 'Memory seats',
        ]);

        $techAmenities = $this->getAmenitiesByNames($amenityIds, [
            'Touchscreen display', 'Apple CarPlay', 'Android Auto', 'Navigation system',
            'Heads-up display', 'Digital instrument cluster', 'Voice control', 'Wireless charging',
        ]);

        $safetyAmenities = $this->getAmenitiesByNames($amenityIds, [
            'Adaptive cruise control', 'Lane departure warning', 'Blind spot monitoring',
            'Parking sensors', 'Backup camera', 'Automatic emergency braking', 'Cross-traffic alert',
        ]);

        $luxuryAmenities = $this->getAmenitiesByNames($amenityIds, [
            'Ventilated seats', 'Massage seats', 'Panoramic roof', 'Ambient lighting',
            '360-degree camera', 'Power tailgate', 'Heated mirrors', 'Auto-dimming mirrors',
        ]);

        $performanceAmenities = $this->getAmenitiesByNames($amenityIds, [
            'All-wheel drive', 'Sport mode', 'Paddle shifters', 'Adaptive suspension',
            'Electronic stability control', 'Traction control', 'Hill start assist',
        ]);

        $selectedAmenities = [];

        // Basic amenities for all cars (3-5 amenities)
        $selectedAmenities = array_merge(
            $selectedAmenities,
            $this->getRandomAmenities($basicAmenities, rand(3, 5))
        );

        // Add amenities based on rental rate (price tier)
        if ($car->rental_rate >= 80) {
            // Luxury cars (80+/day) - get premium amenities
            $selectedAmenities = array_merge(
                $selectedAmenities,
                $this->getRandomAmenities($luxuryAmenities, rand(4, 6)),
                $this->getRandomAmenities($techAmenities, rand(4, 6)),
                $this->getRandomAmenities($safetyAmenities, rand(3, 5)),
                $this->getRandomAmenities($performanceAmenities, rand(2, 4))
            );
        } elseif ($car->rental_rate >= 60) {
            // Premium cars (60-79/day) - good amenities
            $selectedAmenities = array_merge(
                $selectedAmenities,
                $this->getRandomAmenities($comfortAmenities, rand(3, 5)),
                $this->getRandomAmenities($techAmenities, rand(3, 5)),
                $this->getRandomAmenities($safetyAmenities, rand(2, 4)),
                $this->getRandomAmenities($luxuryAmenities, rand(1, 3))
            );
        } elseif ($car->rental_rate >= 40) {
            // Mid-range cars (40-59/day) - standard amenities
            $selectedAmenities = array_merge(
                $selectedAmenities,
                $this->getRandomAmenities($comfortAmenities, rand(2, 4)),
                $this->getRandomAmenities($techAmenities, rand(2, 4)),
                $this->getRandomAmenities($safetyAmenities, rand(1, 3))
            );
        } else {
            // Economy cars (under 40/day) - basic amenities only
            $selectedAmenities = array_merge(
                $selectedAmenities,
                $this->getRandomAmenities($comfortAmenities, rand(1, 2)),
                $this->getRandomAmenities($techAmenities, rand(1, 2))
            );
        }

        // Add performance amenities for sport cars
        if (stripos($car->name, 'sport') !== false ||
            stripos($car->name, 'M ') !== false ||
            stripos($car->name, 'AMG') !== false ||
            stripos($car->name, 'RS') !== false ||
            stripos($car->name, 'GT') !== false) {
            $selectedAmenities = array_merge(
                $selectedAmenities,
                $this->getRandomAmenities($performanceAmenities, rand(2, 4))
            );
        }

        // Remove duplicates and return
        return array_unique($selectedAmenities);
    }

    /**
     * Get amenity IDs by their names
     */
    protected function getAmenitiesByNames(array $amenityIds, array $names): array
    {
        return CarAmenity::query()
            ->whereIn('name', $names)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get random amenities from a list
     */
    protected function getRandomAmenities(array $amenities, int $count): array
    {
        if (empty($amenities)) {
            return [];
        }

        $count = min($count, count($amenities));
        $randomKeys = array_rand($amenities, $count);

        if ($count === 1) {
            return [$amenities[$randomKeys]];
        }

        $result = [];
        foreach ($randomKeys as $key) {
            $result[] = $amenities[$key];
        }

        return $result;
    }

    /**
     * Generate a random address
     */
    protected function generateRandomAddress(): string
    {
        $streetNumbers = range(100, 99999);
        $streetNames = [
            'Oak', 'Maple', 'Cedar', 'Pine', 'Elm', 'Birch', 'Willow', 'Ash', 'Spruce', 'Cherry',
            'Main', 'First', 'Second', 'Third', 'Park', 'Washington', 'Jefferson', 'Madison', 'Lincoln',
            'Roosevelt', 'Wilson', 'Jackson', 'Grant', 'Harrison', 'Taylor', 'Johnson', 'Smith', 'Brown',
            'Miller', 'Davis', 'Jones', 'Wilson', 'Moore', 'Anderson', 'Thomas', 'Martin', 'Thompson',
            'White', 'Harris', 'Clark', 'Lewis', 'Walker', 'Hall', 'Allen', 'Young', 'King', 'Wright',
            'Hill', 'Green', 'Baker', 'Adams', 'Nelson', 'Carter', 'Mitchell', 'Roberts', 'Turner',
            'Phillips', 'Campbell', 'Parker', 'Evans', 'Edwards', 'Collins', 'Stewart', 'Morris',
            'Murphy', 'Rivera', 'Cook', 'Rogers', 'Morgan', 'Peterson', 'Cooper', 'Reed', 'Bailey',
            'Bell', 'Gomez', 'Kelly', 'Howard', 'Ward', 'Cox', 'Diaz', 'Richardson', 'Wood', 'Watson',
            'Brooks', 'Bennett', 'Gray', 'James', 'Reyes', 'Cruz', 'Hughes', 'Price', 'Myers', 'Long',
            'Foster', 'Sanders', 'Ross', 'Morales', 'Powell', 'Sullivan', 'Russell', 'Ortiz', 'Jenkins',
            'Gutierrez', 'Perry', 'Butler', 'Barnes', 'Fisher', 'Oren', 'Lake', 'River', 'Mountain',
        ];

        $streetTypes = [
            'Street', 'Avenue', 'Road', 'Boulevard', 'Drive', 'Lane', 'Court', 'Place', 'Way', 'Circle',
            'Trail', 'Parkway', 'Highway', 'Plaza', 'Terrace', 'Ridge', 'Pass', 'Point', 'View', 'Heights',
            'Cliffs', 'Landing', 'Grove', 'Gardens', 'Walk', 'Park', 'Hills', 'Valley', 'Creek', 'Meadows',
        ];

        $unitTypes = ['Suite', 'Apt', 'Unit', 'Floor', '#'];

        // Generate base address
        $number = $streetNumbers[array_rand($streetNumbers)];
        $name = $streetNames[array_rand($streetNames)];
        $type = $streetTypes[array_rand($streetTypes)];

        $address = $number . ' ' . $name . ' ' . $type;

        // Sometimes add a unit number (30% chance)
        if (rand(1, 10) <= 3) {
            $unitType = $unitTypes[array_rand($unitTypes)];
            $unitNumber = rand(1, 999);
            $address .= ' ' . $unitType . ' ' . $unitNumber;
        }

        return $address;
    }
}
