<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * Build system prompt from database settings with live data
     * Full prompt is cached for 1 hour to avoid database hits on every message
     * Cache is cleared when admin updates any setting from admin panel
     */
    private function buildSystemPrompt(): string
    {
        // Return cached entire prompt if available (no database hits!)
        $cachedPrompt = Cache::get('chat_system_prompt_full');
        if ($cachedPrompt) {
            return $cachedPrompt;
        }

        // Build prompt only if cache expired or cleared by admin
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

        // Get live data
        $fleetInfo = $this->getFleetInfo();
        $livePrice = $this->getLivePricing();
        $carTypes = $this->getCarTypes();
        $inventory = $this->getInventoryStatus();

        $prompt = <<<PROMPT
{$basePrompt}

=== ABOUT {$companyName} ===
{$companyDesc}

CURRENT LIVE DATA:
{$fleetInfo}
{$carTypes}
{$inventory}
{$livePrice}

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

        // Cache entire prompt for 1 hour to reduce database queries
        // This cache is cleared automatically when admin updates any setting
        Cache::put('chat_system_prompt_full', $prompt, 3600);

        return $prompt;
    }

    /**
     * Get live fleet information from database (cached 15 min)
     */
    private function getFleetInfo(): string
    {
        return Cache::remember('chat_fleet_info', 900, function () {
            $cars = DB::table('cr_cars')
                ->select('name')
                ->where('status', 'available')
                ->limit(5)
                ->get();

            if ($cars->isEmpty()) {
                return "Currently updating fleet information.\n";
            }

            $fleetInfo = "Available vehicles:\n";
            foreach ($cars as $car) {
                $fleetInfo .= "• {$car->name}\n";
            }
            return $fleetInfo;
        });
    }

    /**
     * Get live pricing information (cached 15 min)
     */
    private function getLivePricing(): string
    {
        return Cache::remember('chat_live_pricing', 900, function () {
            $pricing = DB::table('cr_cars')
                ->select('rental_rate')
                ->where('status', 'available')
                ->orderBy('rental_rate')
                ->get();

            if ($pricing->isEmpty()) {
                return "Contact support for pricing.";
            }

            $minPrice = $pricing->min('rental_rate');
            $maxPrice = $pricing->max('rental_rate');
            $avgPrice = round($pricing->avg('rental_rate'), 2);

            return "LIVE PRICING: Minimum={$minPrice}/day | Average={$avgPrice}/day | Premium={$maxPrice}/day";
        });
    }

    /**
     * Get available car types (cached 60 min)
     */
    private function getCarTypes(): string
    {
        return Cache::remember('chat_car_types', 3600, function () {
            $count = DB::table('cr_cars')
                ->where('status', 'available')
                ->count();
            
            return "Total vehicle categories available. We have $count vehicles ready for rental.";
        });
    }

    /**
     * Get inventory status (cached 10 min)
     */
    private function getInventoryStatus(): string
    {
        return Cache::remember('chat_inventory_status', 600, function () {
            $total = DB::table('cr_cars')->count();
            $available = DB::table('cr_cars')->where('status', 'available')->count();
            $booked = $total - $available;

            return "FLEET: Total=$total | Available=$available | Booked=$booked";
        });
    }
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'conversation_id' => 'nullable|integer|exists:chat_conversations,id',
            'session_token' => 'nullable|string',
            'user_id' => 'nullable|integer',  // Accept user ID from frontend
        ]);

        // Get user ID - prefer from request (frontend provides this), fall back to session auth
        $userId = $validated['user_id'] ?? null;
        $user = null;

        // If frontend provided a user_id, verify it's legitimate
        if ($userId) {
            // Check if there's an authenticated user
            $authenticatedUser = Auth::guard('customer')->user() 
                                 ?? Auth::guard('web')->user() 
                                 ?? $request->user();
            
            // Only use the provided user_id if it matches the authenticated user
            // This prevents users from spoofing other user IDs
            if ($authenticatedUser && $authenticatedUser->id === $userId) {
                $user = $authenticatedUser;
            } else if ($authenticatedUser) {
                // User provided wrong ID - use authenticated user instead
                $userId = $authenticatedUser->id;
                $user = $authenticatedUser;
            }
            // If no authenticated user and user_id provided, accept it (guest with explicit ID)
        } else {
            // No user_id provided, try to get from session
            $user = Auth::guard('customer')->user() 
                     ?? Auth::guard('web')->user() 
                     ?? $request->user();
            $userId = $user?->id;
        }

        \Log::info('Chat Send Request', [
            'user_id' => $userId,
            'user_name' => $user?->name ?? 'Guest',
            'authenticated' => $user ? 'YES' : 'NO',
        ]);

        // Load or create conversation
        if ($validated['conversation_id'] ?? null) {
            $conversation = ChatConversation::findOrFail($validated['conversation_id']);
            
            // Associate user with conversation if not already associated and user is logged in
            if (!$conversation->user_id && $userId) {
                $conversation->update(['user_id' => $userId]);
            }
        } else {
            // Use provided session token or create new one
            $sessionToken = $validated['session_token'] ?? uniqid('chat_', true);
            
            $conversation = ChatConversation::firstOrCreate(
                ['session_id' => $sessionToken],
                [
                    'user_id' => $userId,
                ]
            );
            
            // Debug log for new conversation
            \Log::info('New Conversation Created', [
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'user_name' => $user?->name ?? 'Guest',
                'session_id' => $conversation->session_id,
            ]);
        }

        // Save user message
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        // Build system prompt from database settings
        $promptContent = $this->buildSystemPrompt();

        $systemPrompt = [
            'role' => 'system',
            'content' => $promptContent
        ];

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();

        // Prepend system prompt to conversation
        array_unshift($messages, $systemPrompt);

        // Call OpenRouter API
        try {
            $response = Http::withToken(config('services.openrouter.key'))
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-OpenRouter-Title' => config('app.name'),
                ])
                ->timeout(15)
                ->connectTimeout(10)
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model'),
                    'messages' => $messages,
                ]);

            if ($response->failed()) {
                \Log::warning('OpenRouter API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'assistant' => 'I am temporarily unavailable. Please try again in a moment.'
                ], 200);
            }

            $assistantMessage = $response->json('choices.0.message.content') ?? '';

            if (!$assistantMessage) {
                \Log::warning('No message in OpenRouter response', ['response' => $response->json()]);
                return response()->json([
                    'assistant' => 'I received an unclear response. Could you rephrase your question?'
                ], 200);
            }

            // Save assistant response
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $assistantMessage,
            ]);

            return response()->json([
                'assistant' => $assistantMessage,
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,  // Debug: show user_id in response
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('OpenRouter connection error', ['error' => $e->getMessage()]);
            return response()->json([
                'assistant' => 'Network connection error. Please try again.'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Chat API Error', ['error' => $e->getMessage()]);
            return response()->json([
                'assistant' => 'An error occurred. Please try again.'
            ], 200);
        }
    }

    /**
     * Get conversation history
     */
    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
            'user_id' => 'nullable|integer',  // Optional user ID from frontend
        ]);

        $conversation = ChatConversation::findOrFail($validated['conversation_id']);
        
        // Get current user - either from provided user_id or from session
        $userId = $validated['user_id'] ?? null;
        $user = null;

        if ($userId) {
            // If user_id provided, verify it matches authenticated user
            $authenticatedUser = Auth::guard('customer')->user() 
                                 ?? Auth::guard('web')->user() 
                                 ?? $request->user();
            if ($authenticatedUser && $authenticatedUser->id === $userId) {
                $user = $authenticatedUser;
            } else if ($authenticatedUser) {
                $userId = $authenticatedUser->id;
                $user = $authenticatedUser;
            }
        } else {
            $user = Auth::guard('customer')->user() 
                     ?? Auth::guard('web')->user() 
                     ?? $request->user();
            $userId = $user?->id;
        }

        \Log::info('Chat History Request', [
            'user_id' => $userId,
            'conversation_user_id' => $conversation->user_id,
            'user_name' => $user?->name ?? 'Guest',
        ]);

        // If conversation has no user_id but user is authenticated, associate them
        if (!$conversation->user_id && $userId) {
            $conversation->update(['user_id' => $userId]);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
                'timestamp' => $msg->created_at,
            ])
            ->toArray();

        return response()->json([
            'messages' => $messages,
            'conversation_id' => $conversation->id,
        ]);
    }

    /**
     * Get suggested questions for the chat widget (cached)
     * Cache is cleared when admin updates questions
     */
    public function suggestedQuestions(Request $request): JsonResponse
    {
        // Check cache first (60 minute TTL)
        $questions = Cache::get('chat_suggested_questions');
        
        if ($questions !== null) {
            return response()->json(['questions' => $questions]);
        }
        
        // Get suggested questions from settings (stored as JSON)
        $questionsJson = ChatSetting::get('suggested_questions', '[]');
        
        // If it's a string, decode it; if it's already an array, use it
        $questions = is_string($questionsJson) ? json_decode($questionsJson, true) : $questionsJson;
        
        // Ensure it's an array
        if (!is_array($questions) || empty($questions)) {
            // Fallback to default questions if none configured
            $questions = [
                'What vehicles do you have available?',
                'What are your rental prices?',
                'How do I book a car?',
                'Do you have luxury vehicles?',
                'What is your cancellation policy?'
            ];
        }
        
        // Cache for 60 minutes
        Cache::put('chat_suggested_questions', $questions, now()->addMinutes(60));
        
        return response()->json(['questions' => $questions]);
    }
}
