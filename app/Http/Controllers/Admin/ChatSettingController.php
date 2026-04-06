<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatSettingController extends Controller
{
    /**
     * Display all settings grouped by section
     */
    public function index()
    {
        $sections = ChatSetting::distinct()->pluck('section');
        $settings = [];
        
        foreach ($sections as $section) {
            $settings[$section] = ChatSetting::where('section', $section)->get();
        }

        return view('admin.chat-settings.index', compact('settings'));
    }

    /**
     * Update a specific setting
     */
    public function update(Request $request, ChatSetting $setting)
    {
        $validated = $request->validate([
            'value' => 'required|string',
        ]);

        ChatSetting::set($setting->key, $validated['value']);
        
        // Clear the full prompt cache so next chat message regenerates it with new value
        Cache::forget('chat_system_prompt_full');

        return back()->with('success', "Setting '{$setting->key}' updated successfully!");
    }

    /**
     * Bulk update settings via JSON
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:chat_settings,key',
            'settings.*.value' => 'required|string',
        ]);

        foreach ($validated['settings'] as $item) {
            ChatSetting::set($item['key'], $item['value']);
        }

        // Clear the full prompt cache so next chat regenerates with new settings
        Cache::forget('chat_system_prompt_full');

        return response()->json(['message' => 'Settings updated successfully!']);
    }

    /**
     * Preview the AI prompt
     */
    public function previewPrompt()
    {
        $companyName = ChatSetting::get('company_name', 'MXCar');
        $companyDesc = ChatSetting::get('company_description', '');
        $supportEmail = ChatSetting::get('support_email', 'support@company.com');
        $supportPhone = ChatSetting::get('support_phone', '+1-800-000-0000');
        $cancellationPolicy = ChatSetting::get('cancellation_policy', '');
        $pricingInfo = ChatSetting::get('pricing_info', '');
        $insuranceInfo = ChatSetting::get('insurance_info', '');
        $basePrompt = ChatSetting::get('base_prompt', '');

        // Replace placeholders in base prompt
        $basePrompt = str_replace(
            ['{COMPANY_NAME}', '{SUPPORT_EMAIL}', '{SUPPORT_PHONE}'],
            [$companyName, $supportEmail, $supportPhone],
            $basePrompt
        );

        $prompt = <<<PROMPT
{$basePrompt}

=== ABOUT {$companyName} ===
{$companyDesc}

=== PRICING & RENTAL OPTIONS ===
{$pricingInfo}
• Full tank included: All vehicles provided with full fuel tank
• Fuel policy: Return full or pay for fuel used

=== INSURANCE & COVERAGE ===
{$insuranceInfo}

=== POLICIES & GUARANTEES ===
• Cancellation Policy: {$cancellationPolicy}
• Late Returns: Additional charges apply for late returns
• Mileage: Unlimited mileage on most rentals
• Age Requirements: Valid driver's license required
• Damage Reports: All damage documented at pickup/return

=== PICK-UP & DROP-OFF ===
• Multiple convenient locations:
  - Major airports
  - City centers
  - Train stations
• Same-location returns
• Airport drop-off options available

=== SPECIAL FEATURES ===
We offer:
• GPS/Navigation systems
• Child safety seats
• Extra fuel arrangements
• Additional drivers
• Loyalty Program with points & rewards
• 24/7 roadside assistance
• Exclusive deals and seasonal promotions

=== PAYMENT & SUPPORT ===
• Email: {$supportEmail}
• Phone: {$supportPhone}
• 24/7 Customer Support Available
• Secure payment gateway
• All major credit/debit cards accepted
• PayPal & digital payment options

=== YOUR ROLE ===
Provide professional, friendly assistance with:
1. Vehicle selection based on customer needs
2. Accurate pricing and availability information
3. Clearly explaining rental policies
4. Booking assistance and modifications
5. Insurance and coverage options
6. Special requests and accommodations
7. Problem resolution and complaints
8. Contact information for permanent issues

Represent {$companyName} as customer-focused, reliable, and professional. When unsure about specifics, direct customers to call {$supportPhone} or email {$supportEmail} for immediate assistance.

Prioritize:
• Customer satisfaction
• Clear, accurate information
• Honest recommendations
• Professional communication
PROMPT;

        return response()->json(['prompt' => $prompt]);
    }

    /**
     * Update suggested questions
     */
    public function updateQuestions(Request $request)
    {
        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string|min:3|max:255',
        ]);

        $questionsJson = json_encode($validated['questions']);
        ChatSetting::set('suggested_questions', $questionsJson);
        
        // Clear the suggested questions cache so next request gets fresh data
        Cache::forget('chat_suggested_questions');
        
        // Also clear the full prompt cache in case it's referenced
        Cache::forget('chat_system_prompt_full');

        return response()->json([
            'success' => true,
            'message' => 'Suggested questions updated successfully! ✓'
        ]);
    }

    /**
     * Reset to default settings
     */
    public function reset(Request $request)
    {
        Cache::flush();
        
        return back()->with('success', 'All cache cleared successfully!');
    }
}
