<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Database\Traits\HasCarSeeder;

class CarSeeder extends BaseSeeder
{
    use HasCarSeeder;

    private ?string $carContent = null;

    public function run(): void
    {
        $this->uploadFiles('cars');

        $this->carContent = file_get_contents(database_path('seeders/contents/car.html'));

        $cars = [
            [
                'license_plate' => '30A-123.00',
                'name' => 'Toyota Camry XLE Hybrid 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.11',
                'name' => 'Honda Accord Sport 2.0T 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-123.22',
                'name' => 'Mercedes-Benz C300 4MATIC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.33',
                'name' => 'BMW 330i xDrive M Sport 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.44',
                'name' => 'Lexus ES 350 F Sport 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.55',
                'name' => 'Toyota RAV4 Prime XSE AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.66',
                'name' => 'Honda CR-V Touring Hybrid AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.77',
                'name' => 'BMW X5 xDrive40i M Sport 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-123.88',
                'name' => 'Mercedes-Benz GLC 300 4MATIC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-123.99',
                'name' => 'Lexus RX 350 F Sport Handling AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.00',
                'name' => 'Audi A4 Premium Plus quattro 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.11',
                'name' => 'Mazda CX-5 2.5 Turbo Signature AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.22',
                'name' => 'Tesla Model 3 Long Range AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.33',
                'name' => 'Porsche Macan S 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.44',
                'name' => 'Volvo XC60 B6 Ultimate AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.55',
                'name' => 'Genesis G70 3.3T Sport Prestige AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.66',
                'name' => 'Subaru Outback Limited XT 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.77',
                'name' => 'Acura MDX Type S Advance 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.88',
                'name' => 'Range Rover Evoque P250 S 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30A-456.99',
                'name' => 'Infiniti QX60 Autograph AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.00',
                'name' => 'Nissan Altima SR VC-Turbo 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.11',
                'name' => 'Hyundai Sonata N Line 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.22',
                'name' => 'Kia Stinger GT2 AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.33',
                'name' => 'Volkswagen Atlas Cross Sport SEL Premium 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.44',
                'name' => 'Chevrolet Blazer RS AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.55',
                'name' => 'Ford Explorer ST 4WD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.66',
                'name' => 'Jeep Grand Cherokee Summit Reserve 4xe 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.77',
                'name' => 'Cadillac XT5 Premium Luxury AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.88',
                'name' => 'Lincoln Aviator Reserve AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29A-789.99',
                'name' => 'Alfa Romeo Stelvio Veloce AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.00',
                'name' => 'McLaren 720S Spider 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.11',
                'name' => 'Mercedes-Benz G550 4MATIC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.22',
                'name' => 'Ford Mustang GT Premium 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.33',
                'name' => 'Porsche 911 Carrera S 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.44',
                'name' => 'Audi R8 V10 Performance 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.55',
                'name' => 'Lamborghini Huracán EVO 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.66',
                'name' => 'BMW M4 Competition xDrive 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.77',
                'name' => 'Maserati Ghibli Modena 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.88',
                'name' => 'Bentley Continental GT V8 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30B-100.99',
                'name' => 'Rolls-Royce Ghost Black Badge 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.00',
                'name' => 'Hyundai Tucson Limited AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.11',
                'name' => 'Kia Sportage SX-Turbo AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.22',
                'name' => 'Volkswagen Tiguan SEL R-Line 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.33',
                'name' => 'Toyota Highlander Platinum AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.44',
                'name' => 'Honda Pilot TrailSport AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.55',
                'name' => 'Mazda MX-5 Miata Grand Touring 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.66',
                'name' => 'Nissan Rogue Platinum AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.77',
                'name' => 'Mini Cooper S Hardtop 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.88',
                'name' => 'Dodge Challenger R/T Scat Pack 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29B-200.99',
                'name' => 'Chevrolet Camaro SS 1LE 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.00',
                'name' => 'Ferrari F8 Tributo 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.11',
                'name' => 'Jaguar F-PACE SVR 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.22',
                'name' => 'Land Rover Defender 110 X 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.33',
                'name' => 'Chrysler Pacifica Pinnacle AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.44',
                'name' => 'GMC Sierra 1500 Denali 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.55',
                'name' => 'Mitsubishi Outlander SEL S-AWC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.66',
                'name' => 'Peugeot 308 GT Premium 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.77',
                'name' => 'Fiat 500X Sport AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.88',
                'name' => 'Smart EQ fortwo Prime 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30C-300.99',
                'name' => 'Toyota Corolla Hybrid LE 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30D-400.00',
                'name' => 'Aston Martin Vantage F1 Edition 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30D-400.11',
                'name' => 'Bugatti Chiron Super Sport 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30D-400.22',
                'name' => 'McLaren Artura Spider 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30D-400.33',
                'name' => 'Pagani Huayra Roadster BC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30D-400.44',
                'name' => 'Koenigsegg Jesko Absolut 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.00',
                'name' => 'Mercedes-Benz E450 4MATIC All-Terrain 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.11',
                'name' => 'Audi Q7 55 TFSI quattro Premium Plus 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.22',
                'name' => 'BMW X7 xDrive40i M Sport 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.33',
                'name' => 'Lexus LX 600 Ultra Luxury 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.44',
                'name' => 'Volvo S90 B6 Ultimate AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.55',
                'name' => 'Genesis GV70 2.5T Sport AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.66',
                'name' => 'Acura TLX Type S PMC Edition 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.77',
                'name' => 'Infiniti Q50 Red Sport 400 AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.88',
                'name' => 'Jaguar XF P300 R-Dynamic S AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30E-500.99',
                'name' => 'Alfa Romeo Giulia Quadrifoglio 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.00',
                'name' => 'Porsche Cayenne Turbo GT 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.11',
                'name' => 'Range Rover Sport HSE Dynamic 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.22',
                'name' => 'Mercedes-AMG GLE 63 S 4MATIC+ 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.33',
                'name' => 'BMW M5 Competition xDrive 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.44',
                'name' => 'Audi RS Q8 quattro 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.55',
                'name' => 'Tesla Model S Plaid 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.66',
                'name' => 'Lucid Air Grand Touring Performance 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.77',
                'name' => 'Rivian R1S Adventure 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.88',
                'name' => 'Polestar 2 Long Range Dual Motor 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29E-600.99',
                'name' => 'Genesis Electrified GV70 Advanced AWD 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.00',
                'name' => 'Mercedes-Maybach S 580 4MATIC 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.11',
                'name' => 'Bentley Bentayga S V8 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.22',
                'name' => 'Rolls-Royce Cullinan Black Badge 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.33',
                'name' => 'Aston Martin DBX707 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.44',
                'name' => 'Maserati Levante Trofeo 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.55',
                'name' => 'Ferrari Roma Spider 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.66',
                'name' => 'McLaren GT Luxe 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.77',
                'name' => 'Lamborghini Urus Performante 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.88',
                'name' => 'Porsche Taycan Turbo S Cross Turismo 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '30F-700.99',
                'name' => 'BMW XM Label Red 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29F-800.00',
                'name' => 'Chevrolet Corvette Z06 3LZ 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29F-800.11',
                'name' => 'Ford GT Heritage Edition 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29F-800.22',
                'name' => 'Dodge Viper ACR Extreme 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29F-800.33',
                'name' => 'Nissan GT-R NISMO Special Edition 2024',
                'insurance_info' => '',
                'location' => '',
            ],
            [
                'license_plate' => '29F-800.44',
                'name' => 'Toyota Supra 3.0 Premium 2024',
                'insurance_info' => '',
                'location' => '',
            ],
        ];

        $this->createCars($cars);
    }
}
