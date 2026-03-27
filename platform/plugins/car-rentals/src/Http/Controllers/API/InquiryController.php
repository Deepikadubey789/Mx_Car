<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InquiryController extends BaseApiController
{
    /**
     * Submit an inquiry
     *
     * @group Car Rentals - Inquiries
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string', 'max:2000'],
                'car_id' => ['nullable', 'exists:cr_cars,id'],
                'vendor_id' => ['nullable', 'exists:cr_customers,id'],
                'inquiry_type' => ['required', 'in:general,car_inquiry,rental_question,support'],
            ]);
        } catch (ValidationException $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(422)
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()])
                ->toApiResponse();
        }

        // Validate car exists and belongs to vendor if both provided
        if ($validated['car_id'] && $validated['vendor_id']) {
            $car = Car::query()
                ->where('id', $validated['car_id'])
                ->where('author_id', $validated['vendor_id'])
                ->first();

            if (! $car) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setCode(422)
                    ->setMessage('Car does not belong to the specified vendor')
                    ->toApiResponse();
            }
        }

        // Validate vendor exists if provided
        if ($validated['vendor_id']) {
            $vendor = Customer::query()
                ->where('id', $validated['vendor_id'])
                ->where('is_vendor', true)
                ->first();

            if (! $vendor) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setCode(422)
                    ->setMessage('Vendor not found')
                    ->toApiResponse();
            }
        }

        // Create the inquiry message
        $messageData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'content' => $validated['message'],
            'status' => 'unread',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add car reference if provided
        if (! empty($validated['car_id'])) {
            $messageData['car_id'] = $validated['car_id'];
        }

        // Add vendor reference if provided
        if (! empty($validated['vendor_id'])) {
            $messageData['vendor_id'] = $validated['vendor_id'];
        }

        $message = Message::query()->create($messageData);

        // TODO: Send email notification to admin/vendor
        // TODO: Send auto-reply confirmation to customer

        $response = [
            'id' => $message->id,
            'reference_number' => 'INQ-' . str_pad($message->id, 6, '0', STR_PAD_LEFT),
            'message' => 'Your inquiry has been submitted successfully. We will get back to you soon.',
            'inquiry_type' => $validated['inquiry_type'],
            'submitted_at' => $message->created_at->toISOString(),
        ];

        if ($validated['car_id']) {
            $car = Car::query()->find($validated['car_id']);
            $response['car'] = [
                'id' => $car->id,
                'name' => $car->name,
                'slug' => $car->slug,
            ];
        }

        if ($validated['vendor_id']) {
            $vendor = Customer::query()->find($validated['vendor_id']);
            $response['vendor'] = [
                'id' => $vendor->id,
                'name' => $vendor->company_name ?: $vendor->first_name . ' ' . $vendor->last_name,
            ];
        }

        return $this
            ->httpResponse()
            ->setData($response)
            ->setMessage('Inquiry submitted successfully')
            ->toApiResponse();
    }
}
