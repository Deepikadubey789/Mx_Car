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

class BookingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService)
    {
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
        $customer = Auth::guard('sanctum')->user();

        $query = Booking::query()
            ->with(['car.car', 'services', 'currency', 'payment', 'invoice']);

        if (is_numeric($id)) {
            // Access by ID - require authentication or guest verification
            $query->where('id', $id);

            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // Guest must provide email for verification
                $request->validate([
                    'email' => ['required', 'email'],
                ]);
                $query->where('customer_email', $request->input('email'));
            }
        } else {
            // Access by booking code - require email
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $query->where('code', $id)
                ->where('customer_email', $request->input('email'));
        }

        $booking = $query->first();

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
        $customer = Auth::guard('sanctum')->user();

        $query = Booking::query();

        if (is_numeric($id)) {
            $query->where('id', $id);

            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // Guest must provide email for verification
                $request->validate([
                    'email' => ['required', 'email'],
                ]);
                $query->where('customer_email', $request->input('email'));
            }
        } else {
            // Access by booking code
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $query->where('code', $id)
                ->where('customer_email', $request->input('email'));
        }

        $booking = $query->first();

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
    public function cancel($id, Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $query = Booking::query();

        if (is_numeric($id)) {
            $query->where('id', $id);

            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // Guest must provide email for verification
                $request->validate([
                    'email' => ['required', 'email'],
                ]);
                $query->where('customer_email', $request->input('email'));
            }
        } else {
            // Access by booking code
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $query->where('code', $id)
                ->where('customer_email', $request->input('email'));
        }

        $booking = $query->first();

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Booking not found')
                ->toApiResponse();
        }

        if (! in_array($booking->status, [BookingStatusEnum::PENDING, BookingStatusEnum::CONFIRMED])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Cannot cancel booking in current status')
                ->toApiResponse();
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $booking->update([
                'status' => BookingStatusEnum::CANCELLED,
                'note' => $booking->note . "\n\nCancellation reason: " . $request->input('reason', 'No reason provided'),
            ]);

            return $this
                ->httpResponse()
                ->setData(new BookingDetailResource($booking->fresh()))
                ->setMessage('Booking cancelled successfully')
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
     * Delete booking
     *
     * @group Car Rentals
     */
    public function destroy($id, Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $query = Booking::query();

        if (is_numeric($id)) {
            $query->where('id', $id);

            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // Guest must provide email for verification
                $request->validate([
                    'email' => ['required', 'email'],
                ]);
                $query->where('customer_email', $request->input('email'));
            }
        } else {
            // Access by booking code
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $query->where('code', $id)
                ->where('customer_email', $request->input('email'));
        }

        $booking = $query->first();

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
        $customer = Auth::guard('sanctum')->user();

        $query = Booking::query()
            ->with(['invoice']);

        if (is_numeric($id)) {
            $query->where('id', $id);

            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // Guest must provide email for verification
                $request->validate([
                    'email' => ['required', 'email'],
                ]);
                $query->where('customer_email', $request->input('email'));
            }
        } else {
            // Access by booking code
            $request->validate([
                'email' => ['required', 'email'],
            ]);
            $query->where('code', $id)
                ->where('customer_email', $request->input('email'));
        }

        $booking = $query->first();

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
                'download_url' => route('car-rentals.invoices.download', $booking->invoice->id),
            ])
            ->toApiResponse();
    }
}
