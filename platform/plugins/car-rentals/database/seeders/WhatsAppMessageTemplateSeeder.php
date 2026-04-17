<?php

namespace Botble\CarRentals\Database\Seeders;

use Botble\CarRentals\Services\WhatsApp\WhatsAppMessageTemplateService;
use Illuminate\Database\Seeder;

class WhatsAppMessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(WhatsAppMessageTemplateService::class);
        $service->seedDefaultTemplates();
        $this->command->info('WhatsApp message templates seeded successfully!');
    }
}
