<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Services\WhatsApp\WhatsAppMessageTemplateService;
use Illuminate\Console\Command;

class SeedWhatsAppTemplatesCommand extends Command
{
    protected $signature = 'whatsapp:seed-templates';
    protected $description = 'Seed default WhatsApp message templates';

    protected WhatsAppMessageTemplateService $templateService;

    public function __construct(WhatsAppMessageTemplateService $templateService)
    {
        parent::__construct();
        $this->templateService = $templateService;
    }

    public function handle(): int
    {
        try {
            $this->templateService->seedDefaultTemplates();
            $this->info('✓ WhatsApp message templates seeded successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Error seeding templates: ' . $e->getMessage());
            return 1;
        }
    }
}
