<?php

namespace Database\Seeders\Themes\Main;

use Botble\ACL\Models\User;
use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('customers');

        Customer::query()->truncate();

        $adminUserId = User::query()->first()?->id;

        $vendorNames = [
            'Elite Auto Group',
            'Premier Motors',
            'Luxury Car Rentals',
            'City Drive Solutions',
            'Express Auto Dealers',
            'Summit Car Company',
            'Horizon Automotive',
            'Prestige Vehicles',
            'Metro Car Center',
            'Global Auto Partners',
            'Royal Motors Group',
            'Diamond Car Dealers',
            'Pacific Auto Sales',
            'Mountain View Motors',
            'Coastal Car Company',
            'Central Auto Hub',
            'Victory Automotive',
            'Alliance Car Group',
            'Phoenix Motors',
            'Sterling Auto Sales',
        ];

        $verificationNotes = [
            'Verified dealership',
            'Authorized car dealer',
            'Premium vendor account',
            'Certified dealer',
            'Trusted automotive partner',
        ];

        foreach ($vendorNames as $index => $vendorName) {
            $imageNumber = 12 + $index;
            $emailPrefix = strtolower(str_replace([' ', "'"], ['-', ''], $vendorName));
            $phone = '+1' . rand(2000000000, 9999999999);
            $customer = [
                'name' => $vendorName,
                'email' => $emailPrefix . '@example.com',
                'phone' => $phone,
                'whatsapp' => $phone,
                'password' => Hash::make('12345678'),
                'avatar' => sprintf('customers/%d.jpg', $imageNumber),
                'is_vendor' => 1,
                'confirmed_at' => Carbon::now(),
            ];

            if (rand(1, 100) <= 70) {
                $customer['vendor_verified_at'] = Carbon::now()->subDays(rand(1, 365));
                $customer['is_verified'] = true;
                $customer['verified_at'] = Carbon::now()->subDays(rand(1, 180));
                $customer['verified_by'] = $adminUserId;
                $customer['verification_note'] = Arr::random($verificationNotes);
            }

            $customers[] = $customer;
        }

        $customerNames = [
            'John Smith',
            'Emily Johnson',
            'Michael Brown',
            'Sarah Davis',
            'David Wilson',
            'Jessica Martinez',
            'Christopher Anderson',
            'Amanda Taylor',
            'Daniel Thomas',
            'Ashley Garcia',
        ];

        $customerVerificationNotes = [
            'Documents verified successfully',
            'Identity confirmed',
            'Verified through government ID',
            'Verified customer - regular client',
            'Trusted customer',
        ];

        foreach ($customerNames as $i => $name) {
            $emailPrefix = strtolower(str_replace(' ', '.', $name));
            $phone = '+1' . rand(2000000000, 9999999999);
            $customer = [
                'name' => $name,
                'email' => $emailPrefix . '@example.com',
                'phone' => $phone,
                'whatsapp' => $phone,
                'password' => Hash::make('12345678'),
                'avatar' => sprintf('customers/%d.jpg', $i + 1),
                'confirmed_at' => Carbon::now(),
            ];

            if (rand(1, 100) <= 40) {
                $customer['is_verified'] = true;
                $customer['verified_at'] = Carbon::now()->subDays(rand(1, 180));
                $customer['verified_by'] = $adminUserId;
                $customer['verification_note'] = Arr::random($customerVerificationNotes);
            }

            $customers[] = $customer;
        }

        $randNumber = rand(1, 10);

        $customers[] = [
            'name' => 'Demo Customer',
            'email' => 'customer@botble.com',
            'phone' => $phone = '+1' . rand(2000000000, 9999999999),
            'whatsapp' => $phone,
            'password' => Hash::make('12345678'),
            'avatar' => sprintf('customers/%d.jpg', $randNumber),
            'confirmed_at' => Carbon::now(),
        ];

        $customers[] = [
            'name' => 'Demo Vendor',
            'email' => 'vendor@botble.com',
            'phone' => $phone = '+1' . rand(2000000000, 9999999999),
            'whatsapp' => $phone,
            'password' => Hash::make('12345678'),
            'avatar' => sprintf('customers/%d.jpg', $randNumber),
            'is_vendor' => 1,
            'vendor_verified_at' => Carbon::now()->subDays(rand(1, 180)),
            'is_verified' => true,
            'verified_at' => Carbon::now()->subDays(rand(1, 90)),
            'verified_by' => $adminUserId,
            'verification_note' => 'Verified vendor account',
            'confirmed_at' => Carbon::now(),
        ];

        foreach ($customers as $customer) {
            Customer::query()->forceCreate($customer);
        }
    }
}
