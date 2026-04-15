<?php

namespace Botble\CarRentals\Http\Controllers\API\Webhook;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\VehicleTelematicsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelematicsWebhookController extends BaseController
{
    /**
     * Handle incoming generic telematics webhooks
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Basic Security Verification (Require a secret header)
        $secretKey = $request->header('X-Telematics-Secret');
        if ($secretKey !== env('TELEMATICS_WEBHOOK_SECRET', 'default-dev-secret-key-123')) {
            Log::warning('Unauthorized Telematics Webhook Attempt', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Extract Generic Payload
        // We expect: { "device_id": "ABC123XYZ", "event": "ping", "lat": 40.71, "lng": -74.00, "speed": 65.5, "odometer": 15000, "fuel": 85 }
        $payload = $request->all();
        $deviceId = $payload['device_id'] ?? null;

        if (!$deviceId) {
            return response()->json(['error' => 'Missing device_id'], 400);
        }

        // 3. Find the associated Car
        $car = Car::where('telematics_device_id', $deviceId)->first();

        if (!$car) {
            Log::info('Webhook received for unknown device ID: ' . $deviceId);
            return response()->json(['error' => 'Device not registered to any car'], 404);
        }

        // 4. Save the Log
        $log = VehicleTelematicsLog::create([
            'car_id' => $car->id,
            'event_type' => $payload['event'] ?? 'periodic_ping',
            'latitude' => $payload['lat'] ?? null,
            'longitude' => $payload['lng'] ?? null,
            'speed_mph' => $payload['speed'] ?? 0,
            'odometer_miles' => $payload['odometer'] ?? null,
            'fuel_percentage' => $payload['fuel'] ?? null,
            'raw_payload' => $payload, // Keep original data for debugging
        ]);

        // 5. Alert Triggering Logic (Example: High Speed)
        if (($payload['speed'] ?? 0) > 85) {
            // Here you would trigger an SMS or Email to the Vendor or Platform Admin
            Log::alert("HIGH SPEED ALERT: Car ID {$car->id} is traveling at {$payload['speed']} mph.");
        }

        return response()->json([
            'success' => true,
            'message' => 'Telematics data logged successfully.',
            'log_id' => $log->id
        ], 200);
    }
}