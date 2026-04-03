<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, number, email, phone
            $table->string('section')->default('general'); // general, company, support, policies, prompt
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $defaults = [
            ['key' => 'company_name', 'value' => 'MXCar', 'type' => 'text', 'section' => 'company', 'description' => 'Company name for AI assistant'],
            ['key' => 'company_description', 'value' => 'MXCar offers a diverse fleet of vehicles for daily, weekly, and monthly rentals. We pride ourselves on transparent pricing, excellent service, and customer satisfaction.', 'type' => 'textarea', 'section' => 'company', 'description' => 'Company description for AI context'],
            ['key' => 'support_email', 'value' => 'support@carento.com', 'type' => 'email', 'section' => 'support', 'description' => 'Support email address'],
            ['key' => 'support_phone', 'value' => '+1 (800) 123-4567', 'type' => 'phone', 'section' => 'support', 'description' => 'Support phone number'],
            ['key' => 'cancellation_policy', 'value' => 'Free cancellation up to 24 hours before pickup', 'type' => 'textarea', 'section' => 'policies', 'description' => 'Cancellation policy details'],
            ['key' => 'base_prompt', 'value' => 'You are {COMPANY_NAME}, a premium car rental company assistant. You provide expert customer service for all car rental needs.', 'type' => 'textarea', 'section' => 'prompt', 'description' => 'Base system prompt (use {COMPANY_NAME}, {SUPPORT_EMAIL}, {SUPPORT_PHONE} as placeholders)'],
            ['key' => 'pricing_info', 'value' => 'Daily rentals: Most flexible option with daily rates. Weekly rentals: Better rates for extended trips. Monthly rentals: Best value for long-term needs.', 'type' => 'textarea', 'section' => 'prompt', 'description' => 'Pricing information for AI'],
            ['key' => 'insurance_info', 'value' => 'Multiple insurance options available. Coverage details provided at booking. Damage protection plans available. Additional drivers can be added.', 'type' => 'textarea', 'section' => 'prompt', 'description' => 'Insurance information for AI'],
        ];

        foreach ($defaults as $setting) {
            \DB::table('chat_settings')->insert($setting + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_settings');
    }
};
