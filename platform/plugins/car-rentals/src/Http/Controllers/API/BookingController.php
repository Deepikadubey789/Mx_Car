<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Events\BookingCreated;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Http\Resources\BookingDetailResource;
use Botble\CarRentals\Http\Resources\BookingResource;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Services\BookingService;
use Botble\CarRentals\Services\PricingQuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Botble\CarRentals\Services\TripModificationService;

class BookingController extends BaseApiController
{
    public function __construct(
        protected BookingService $bookingService,
        protected TripModificationService $tripModificationService,
    ) {
    }

    /**
     * List bookings (authenticated users only see their own, guest access requires booking code + email)
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if ($customer) {
            // Authenticated user - show their bookings
            $query = Booking::query()
                ->where('customer_id', $customer->id)
                ->with(['car.car', 'services', 'currency', 'payment'])->latest();
        } else {
            // Guest user - require booking code and email
            $request->validate([
                'booking_code' => ['required', 'string'],
                'email' => ['required', 'email'],
            ]);

            $query = Booking::query()
                ->where('code', $request->input('booking_code'))
                ->where('customer_email', $request->input('email'))
                ->with(['car.car', 'services', 'currency', 'payment'])->latest();
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->integer('per_page', 10), 50);
        $bookings = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(BookingResource::collection($bookings))
            ->toApiResponse();
    }

    /**
     * Create a new booking (supports both guest and authenticated users)
     *
     * @group Car Rentals
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        if ($customer && (string) $customer->kyc_status !== 'verified') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Please complete KYC verification before booking a car.')
                ->setData([
                    'next_url' => route('customer.kyc'),
                    'kyc_status' => (string) $customer->kyc_status,
                ])
                ->toApiResponse();
        }

        $rules = [
            'car_id' => 'required|exists:cr_cars,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:pickup_date',
            'pickup_time' => 'nullable|string',
            'return_time' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:cr_services,id',
            'coupon_code' => 'nullable|string',
            'note' => 'nullable|string|max:1000',
            'pickup_location_id' => 'nullable|exists:cr_locations,id',
            'return_location_id' => 'nullable|exists:cr_locations,id',
        ];

        // If not authenticated, require customer details
        if (! $customer) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_email'] = 'required|email|max:255';
            $rules['customer_phone'] = 'nullable|string|max:20';
            $rules['customer_address'] = 'nullable|string|max:500';
            $rules['customer_zip_code'] = 'nullable|string|max:20';
        }

        $request->validate($rules);

        $car = Car::find($request->input('car_id'));
        if (! $car || $car->status->getValue() !== 'available') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Car is not available')
                ->toApiResponse();
        }

        $pickupDate = CarRentalsHelper::dateFromRequest($request->input('pickup_date'));
        $returnDate = CarRentalsHelper::dateFromRequest($request->input('return_date'));

        if (! $pickupDate || ! $returnDate) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Invalid date format')
                ->toApiResponse();
        }

        // Check availability
        $dateFormat = CarRentalsHelper::getDateFormat();
        $condition = [
            'start_date' => $pickupDate->format($dateFormat),
            'end_date' => $returnDate->format($dateFormat),
        ];

        if (! $car->isAvailableAt($condition)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Car is not available for the selected dates')
                ->toApiResponse();
        }

        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $pickupDate,
            $returnDate,
            $request->input('services', []),
            [],
            $request->input('coupon_code'),
            $customer
        );

        if (($quoteData['eligibility_state'] ?? null) === 'blocked') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Driver is not eligible for this vehicle category.')
                ->setData(['eligibility_reasons' => $quoteData['eligibility_reasons'] ?? []])
                ->toApiResponse();
        }

        try {
            $bookingData = [
                'customer_id' => $customer?->id,
                'customer_name' => $customer ? $customer->name : $request->input('customer_name'),
                'customer_email' => $customer ? $customer->email : $request->input('customer_email'),
                'customer_phone' => $customer ? $customer->phone : $request->input('customer_phone'),
                'customer_address' => $customer ? $customer->address : $request->input('customer_address'),
                'customer_zip_code' => $customer ? $customer->zip_code : $request->input('customer_zip_code'),
                'car_id' => $car->id,
                'pickup_date' => $pickupDate,
                'return_date' => $returnDate,
                'pickup_time' => $request->input('pickup_time'),
                'return_time' => $request->input('return_time'),
                'pickup_location_id' => $request->input('pickup_location_id'),
                'return_location_id' => get_car_rentals_setting('disable_one_way_rental', false)
                    ? $request->input('pickup_location_id')
                    : $request->input('return_location_id'),
                'services' => $request->input('services', []),
                'coupon_code' => $request->input('coupon_code'),
                'note' => $request->input('note'),
            ];

            $booking = $this->bookingService->createBooking($bookingData);

            event(new BookingCreated($booking));

            return $this
                ->httpResponse()
                ->setData(new BookingDetailResource($booking))
                ->setMessage('Booking created successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Get booking details
     *
     * @group Car Rentals
     */
    public function show($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData(new BookingDetailResource($booking))
            ->toApiResponse();
    }

    /**
     * Update booking
     *
     * @group Car Rentals
     */
    public function update($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        if ($booking->status !== BookingStatusEnum::PENDING) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Cannot update booking in current status')
                ->toApiResponse();
        }

        $request->validate([
            'pickup_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'return_date' => ['sometimes', 'date', 'after:pickup_date'],
            'pickup_time' => ['nullable', 'string'],
            'return_time' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $booking->update($request->only([
                'pickup_date', 'return_date', 'pickup_time', 'return_time', 'note',
            ]));

            return $this
                ->httpResponse()
                ->setData(new BookingDetailResource($booking->fresh()))
                ->setMessage('Booking updated successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Cancel booking
     *
     * @group Car Rentals
     */
    // public function cancel($id, Request $request)
    // {
    //     $customer = Auth::guard('sanctum')->user();

    //     $query = Booking::query();

    //     if (is_numeric($id)) {
    //         $query->where('id', $id);

    //         if ($customer) {
    //             $query->where('customer_id', $customer->id);
    //         } else {
    //             // Guest must provide email for verification
    //             $request->validate([
    //                 'email' => ['required', 'email'],
    //             ]);
    //             $query->where('customer_email', $request->input('email'));
    //         }
    //     } else {
    //         // Access by booking code
    //         $request->validate([
    //             'email' => ['required', 'email'],
    //         ]);
    //         $query->where('code', $id)
    //             ->where('customer_email', $request->input('email'));
    //     }

    //     $booking = $query->first();

    //     if (! $booking) {
    //         return $this
    //             ->httpResponse()
    //             ->setError()
    //             ->setCode(404)
    //             ->setMessage('Booking not found')
    //             ->toApiResponse();
    //     }

    //     if (! in_array($booking->status, [BookingStatusEnum::PENDING, BookingStatusEnum::CONFIRMED])) {
    //         return $this
    //             ->httpResponse()
    //             ->setError()
    //             ->setMessage('Cannot cancel booking in current status')
    //             ->toApiResponse();
    //     }

    //     $request->validate([
    //         'reason' => ['nullable', 'string', 'max:500'],
    //     ]);

    //     try {
    //         $booking->update([
    //             'status' => BookingStatusEnum::CANCELLED,
    //             'note' => $booking->note . "\n\nCancellation reason: " . $request->input('reason', 'No reason provided'),
    //         ]);

    //         return $this
    //             ->httpResponse()
    //             ->setData(new BookingDetailResource($booking->fresh()))
    //             ->setMessage('Booking cancelled successfully')
    //             ->toApiResponse();

    //     } catch (\Exception $e) {
    //         return $this
    //             ->httpResponse()
    //             ->setError()
    //             ->setMessage($e->getMessage())
    //             ->toApiResponse();
    //     }
    // }

    /**
     * Delete booking
     *
     * @group Car Rentals
     */
    public function destroy($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        if ($booking->status !== BookingStatusEnum::CANCELLED) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Can only delete cancelled bookings')
                ->toApiResponse();
        }

        try {
            $booking->delete();

            return $this
                ->httpResponse()
                ->setMessage('Booking deleted successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Get booking invoice
     *
     * @group Car Rentals
     */
    public function getInvoice($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        if (! $booking->invoice) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Invoice not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'invoice_id' => $booking->invoice->id,
                'invoice_number' => $booking->invoice->code,
                'amount' => $booking->amount,
                'status' => $booking->payment->status ?? 'pending',
                'created_at' => $booking->invoice->created_at,
                'download_url' => url('api/v1/car-rentals/bookings/' . $id . '/invoice/download'),
                'view_url'     => url('api/v1/car-rentals/bookings/' . $id . '/invoice/view'),
            ])
            ->toApiResponse();
    }

    /**
     * Download booking invoice
     *
     * @group Car Rentals
     */
    public function downloadInvoice($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking || ! $booking->invoice) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Invoice not found')->toApiResponse();
        }

        return \Botble\CarRentals\Facades\InvoiceHelper::downloadInvoice($booking->invoice);
    }

    /**
     * View/Stream booking invoice safely in Mobile
     *
     * @group Car Rentals
     */
    public function streamInvoice($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking || ! $booking->invoice) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Invoice not found')->toApiResponse();
        }

        return \Botble\CarRentals\Facades\InvoiceHelper::streamInvoice($booking->invoice);
    }

    public function cancel($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->cancelTrip(
            $booking,
            $request->input('reason', ''),
            'customer'
        );

        if (! $result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData([
                'booking'             => new BookingDetailResource($booking->fresh()),
                'cancellation_policy' => $result['cancellation_policy'],
                'refund_amount'       => $result['refund_amount'],
            ])
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    public function extend($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate([
            'new_end_date' => ['required', 'date', 'after:today'],
            'reason'       => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->extendTrip(
            $booking,
            $request->input('new_end_date'),
            $request->input('reason', '')
        );

        if (! $result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData([
                'booking'      => new BookingDetailResource($booking->fresh()),
                'extra_days'   => $result['extra_days'],
                'extra_charge' => $result['extra_charge'],
                'new_end_date' => $result['new_end_date'],
                'new_total'    => $result['new_total'] ?? null,
                'status'       => $result['status'] ?? 'approved',
            ])
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    public function shorten($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate([
            'new_end_date' => ['required', 'date'],
            'reason'       => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->shortenTrip(
            $booking,
            $request->input('new_end_date'),
            $request->input('reason', '')
        );

        if (! $result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData([
                'booking'       => new BookingDetailResource($booking->fresh()),
                'saved_days'    => $result['saved_days'],
                'refund_amount' => $result['refund_amount'],
                'saved_days'    => $result['saved_days'],
                'refund_amount' => $result['refund_amount'],
                'new_end_date'  => $result['new_end_date'],
                'new_total'     => $result['new_total'] ?? null,
            ])
            ->setMessage($result['message'])
            ->toApiResponse();
    }


    public function earlyReturn($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);

        if (! $booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->earlyReturn(
            $booking,
            $request->input('reason', '')
        );

        if (! $result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData([
                'booking'       => new BookingDetailResource($booking->fresh()),
                'saved_days'    => $result['saved_days'],
                'refund_amount' => $result['refund_amount'],
                'new_end_date'  => $result['new_end_date'],
                 'new_total'     => $result['new_total'] ?? null,
            ])
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    public function lateReturn($id, Request $request)
    {
        $booking = $this->findBooking($id, $request);
        if ($booking instanceof JsonResponse) return $booking;

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->lateReturn(
            $booking,
            $request->input('reason', '')
        );

        if (! $result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData([
                'booking'           => new BookingDetailResource($booking->fresh()),
                'extra_hours'       => $result['extra_hours'],
                'late_fee_per_hour' => $result['late_fee_per_hour'],
                'late_charge'       => $result['late_charge'],
                'new_total'         => $result['new_total'],
            ])
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    private function findBooking($id, Request $request): ?Booking
    {
        $customer = Auth::guard('sanctum')->user();
        $query = Booking::query()->with(['car.car', 'services', 'currency', 'payment', 'invoice']);

        if ($customer) {
            $query->where('customer_id', $customer->id)
                  ->where(function ($q) use ($id) {
                      $q->where('id', $id)
                        ->orWhere('transaction_id', $id)
                        ->orWhere('code', $id);
                  });
        } else {
            $request->validate(['email' => ['required', 'email']]);
            $query->where('customer_email', $request->input('email'))
                  ->where(function ($q) use ($id) {
                      $q->where('id', $id)
                        ->orWhere('transaction_id', $id)
                        ->orWhere('code', $id);
                  });
        }

        return $query->first();
    }
    /**
     * Approve modification
     */
    public function approveModification($id, Request $request)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $result = $this->tripModificationService->approveModification($booking);

        if (!$result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setData(['booking' => new BookingDetailResource($booking->fresh())])
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    /**
     * Reject modification
     */
    public function rejectModification($id, Request $request)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $result = $this->tripModificationService->rejectModification(
            $booking,
            $request->input('reason', '')
        );

        if (!$result['success']) {
            return $this->httpResponse()->setError()->setMessage($result['message'])->toApiResponse();
        }

        return $this->httpResponse()
            ->setMessage($result['message'])
            ->toApiResponse();
    }

    /**
     * Estimate booking price for the Mobile App
     *
     * @group Car Rentals - Public
     */
    public function estimateBooking(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cr_cars,id',
            'rental_start_date' => 'required|date',
            'rental_end_date' => 'required|date',
            'service_ids' => 'nullable|array',
            'guest_protection_plan_id' => 'nullable|integer', 
            // --- NEW RULES ---
            'delivery_location_id' => 'nullable|integer|exists:cr_delivery_locations,id',
            'custom_delivery_address' => 'nullable|string|max:255',
        ]);

        $car = \Botble\CarRentals\Models\Car::query()->with('deliveryLocations')->findOrFail($request->input('car_id'));
        
        $startDate = $request->input('rental_start_date') ? \Carbon\Carbon::parse($request->input('rental_start_date')) : null;
        $endDate = $request->input('rental_end_date') ? \Carbon\Carbon::parse($request->input('rental_end_date')) : null;
        $guestProtectionPlanId = $request->input('guest_protection_plan_id') ? (int) $request->input('guest_protection_plan_id') : null;

        $quoteData = app(\Botble\CarRentals\Services\PricingQuoteService::class)->buildQuote(
            $car,
            $startDate,
            $endDate,
            $request->input('service_ids', []),
            $guestProtectionPlanId,
            null,
            \Illuminate\Support\Facades\Auth::guard('sanctum')->user()
        );
        
        $deliveryFee = 0.00;
        $deliveryLocationId = $request->input('delivery_location_id');
        $customAddress = $request->input('custom_delivery_address');

        if ($deliveryLocationId && $car->is_delivery_enabled) {
            $requestedZone = \Botble\CarRentals\Models\DeliveryLocation::find($deliveryLocationId);

            if ($requestedZone && $car->deliveryLocations->contains('id', $requestedZone->id)) {
                
                // 1. Verify custom distance
                if (stripos($requestedZone->name, 'custom') !== false || stripos($requestedZone->name, 'address') !== false) {
                    if (!empty($customAddress)) {
                        $distanceCheck = $this->validateCustomDeliveryDistance($car, $customAddress);
                        
                        if (!$distanceCheck['valid']) {
                            return response()->json([
                                'error' => true,
                                'message' => $distanceCheck['error'],
                            ], 422); // Return 422 Unprocessable Entity
                        }
                    }
                }

                // 2. Calculate Fee & Check Threshold
                $deliveryFee = (float) $requestedZone->fee_amount;
                $rentalDays = (int) $quoteData['rental_days'];
                
                if ($car->free_delivery_days_threshold && $rentalDays >= $car->free_delivery_days_threshold) {
                    $deliveryFee = 0.00;
                }
            }
        }

        return response()->json([
            'error' => false,
            'data' => [
                'subtotal' => (float) $quoteData['subtotal'],
                'delivery_fee' => $deliveryFee,
                'total_amount' => (float) $quoteData['final_payable_amount'] + $deliveryFee,
                'tax_amount' => (float) $quoteData['tax_amount'],
                'deposit_amount' => (float) $quoteData['deposit_amount'],
                'guest_protection_fee' => (float) $quoteData['guest_protection_fee'],
                'rental_days' => (int) $quoteData['rental_days'],
                'is_delivery_free' => ($deliveryFee === 0.00 && $deliveryLocationId) ? true : false,
            ],
            'message' => 'Estimate calculated successfully.'
        ]);
    }

    /**
     * Get coordinates from address and calculate distance to the car.
     */
    private function validateCustomDeliveryDistance($car, $guestAddress)
    {
        $carLat = $car->latitude;
        $carLng = $car->longitude;

        if (!$carLat || !$carLng) {
            $city = $car->city ? $car->city->name : null;
            $state = $car->state ? $car->state->name : null;
            $country = $car->country ? $car->country->name : null;

            $hostAddressString = implode(', ', array_filter([$car->address, $city, $state, $country]));
            $hostFallbackString = implode(', ', array_filter([$city, $state, $country]));

            if (empty($hostFallbackString)) {
                return ['valid' => false, 'error' => __('Host location is missing. Cannot verify delivery distance.')];
            }

            try {
                $hostResp = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withHeaders(['User-Agent' => 'BotbleCarRental/1.0'])
                    ->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'q' => $hostAddressString,
                    'limit' => 1
                ]);

                $hostData = $hostResp->json();

                if (empty($hostData)) {
                    $hostResp = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->withHeaders(['User-Agent' => 'BotbleCarRental/1.0'])
                        ->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $hostFallbackString,
                        'limit' => 1
                    ]);
                    $hostData = $hostResp->json();
                }

                if (empty($hostData)) {
                    return ['valid' => false, 'error' => __('We could not locate the host on the map to verify distance.')];
                }

                $carLat = $hostData[0]['lat'];
                $carLng = $hostData[0]['lon'];
            } catch (\Exception $e) {
                return ['valid' => false, 'error' => 'Host Map API Error: ' . $e->getMessage()];
            }
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['User-Agent' => 'BotbleCarRental/1.0'])
                ->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $guestAddress,
                'limit' => 1
            ]);

            $data = $response->json();

            if (empty($data)) {
                return ['valid' => false, 'error' => __('We could not find your delivery address. Please check for typos.')];
            }

            $guestLat = $data[0]['lat'];
            $guestLng = $data[0]['lon'];

            $earthRadius = 3959; 
            
            $latFrom = deg2rad((float)$carLat);
            $lonFrom = deg2rad((float)$carLng);
            $latTo = deg2rad((float)$guestLat);
            $lonTo = deg2rad((float)$guestLng);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
              cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
              
            $distance = $angle * $earthRadius;
            
            $maxDistance = $car->max_delivery_distance_miles ?? 10;

            if ($distance > $maxDistance) {
                return [
                    'valid' => false, 
                    'error' => __('This address is :dist miles away. Delivery is only available within :max miles of the host\'s location.', ['dist' => round($distance, 1), 'max' => $maxDistance])
                ];
            }

            return ['valid' => true, 'distance' => $distance];

        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Guest Map API Error: ' . $e->getMessage()]; 
        }
    }
}
