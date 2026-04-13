<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Events\BookingCreated;
use Botble\CarRentals\Events\BookingStatusChanged;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\BookingCreateForm;
use Botble\CarRentals\Forms\BookingForm;
use Botble\CarRentals\Http\Requests\CreateBookingRequest;
use Botble\CarRentals\Http\Requests\UpdateBookingCompletionRequest;
use Botble\CarRentals\Http\Requests\UpdateBookingRequest;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Services\DepositHoldSettlementService;
use Botble\CarRentals\Services\PricingQuoteService;
use Botble\CarRentals\Tables\BookingTable;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BookingController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.name'))
            ->add(trans('plugins/car-rentals::booking.name'), route('car-rentals.bookings.index'));
    }

    public function index(BookingTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::booking.name'));

        return $table->renderTable();
    }

    public function edit(Booking $booking)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $booking->car->car_name]));

        return BookingForm::createFromModel($booking)->renderForm();
    }

    public function update(Booking $booking, UpdateBookingRequest $request)
    {
        $status = $booking->status;

        BookingForm::createFromModel($booking)
            ->setRequest($request)
            ->save();

        if ($booking->status != $status) {
            BookingStatusChanged::dispatch($status, $booking);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.bookings.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Booking $booking)
    {
        return DeleteResourceAction::make($booking);
    }

    public function updateCompletion(Booking $booking, UpdateBookingCompletionRequest $request)
    {
        $data = $request->validated();
        $settlementMessage = null;
        $settlementAction = Arr::get($data, 'deposit_settlement_action');
        $settlementCaptureAmount = Arr::get($data, 'deposit_capture_amount');
        $overageAmountForSettlement = (float) ($booking->distance_overage_amount ?? 0);

        if ($booking->deposit_hold_status === 'authorized' && empty($data['deposit_settlement_action'])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.deposit_settlement_action_required'));
        }

        // Handle damage images upload
        if ($request->hasFile('completion_damage_images')) {
            $uploadedImages = [];
            foreach ($request->file('completion_damage_images') as $file) {
                $result = RvMedia::handleUpload($file, 0, 'car-rentals/completion-images');
                if ($result['error'] === false) {
                    $uploadedImages[] = $result['data']->url;
                }
            }

            // Merge with existing images if any
            $existingImages = $request->input('existing_damage_images', []);
            $data['completion_damage_images'] = array_merge($existingImages, $uploadedImages);
        } else {
            // Keep only existing images
            $data['completion_damage_images'] = $request->input('existing_damage_images', []);
        }

        // Set completion timestamp if not already set
        if (! $booking->completed_at && $booking->status == BookingStatusEnum::COMPLETED) {
            $data['completed_at'] = Carbon::now();
        }

        $startMileageBaseline = $booking->start_mileage_snapshot ?? $booking->start_mileage;

        if (Arr::has($data, 'completion_miles') && $data['completion_miles'] !== null && $startMileageBaseline !== null) {
            $completionMiles = (int) $data['completion_miles'];
            $startMileage = (int) $startMileageBaseline;

            if ($completionMiles < $startMileage) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('plugins/car-rentals::booking.validation.completion_miles_less_than_start'));
            }
        }

        if (Arr::has($data, 'completion_miles') && $data['completion_miles'] !== null && $startMileageBaseline !== null) {
            $completionMiles = (int) $data['completion_miles'];
            $startMileage = (int) $startMileageBaseline;
            $travelled = max(0, $completionMiles - $startMileage);
            $includedLimit = max(0, (int) ($booking->included_distance_limit ?? 0));
            $overageUnits = max(0, $travelled - $includedLimit);
            $billingMode = (string) ($booking->distance_overage_billing_mode ?: 'end_of_trip');
            $unitPrice = (float) ($booking->extra_distance_unit_price ?? 0);
            $overageAmount = in_array($billingMode, ['end_of_trip', 'both'], true)
                ? round($overageUnits * $unitPrice, 2)
                : 0.0;

            $data['distance_travelled'] = $travelled;
            $data['distance_overage_units'] = $overageUnits;
            $data['distance_overage_amount'] = $overageAmount;
            $overageAmountForSettlement = $overageAmount;

            if ($booking->car && $booking->car->car_id) {
                Car::query()->whereKey($booking->car->car_id)->update([
                    'mileage' => $completionMiles,
                ]);
            }
        }

        if ($booking->deposit_hold_status === 'authorized' && $settlementAction === 'capture_overage') {
            if ($overageAmountForSettlement <= 0) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('plugins/car-rentals::booking.deposit_capture_overage_no_amount'));
            }

            $authorizedAmount = (float) ($booking->deposit_hold_amount ?: $booking->deposit_amount);
            $settlementAction = 'capture_partial';
            $settlementCaptureAmount = min($overageAmountForSettlement, $authorizedAmount);

            if ($settlementCaptureAmount <= 0) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('plugins/car-rentals::booking.deposit_capture_overage_no_amount'));
            }
        }

        if (Arr::has($data, 'distance_overage_amount')) {
            $baseTripAmount = max(0, round(
                (float) ($booking->sub_total ?? 0)
                + (float) ($booking->tax_amount ?? 0)
                - (float) ($booking->coupon_amount ?? 0)
                + (float) ($booking->fee_amount ?? 0)
                + (float) ($booking->deposit_amount ?? 0),
                2
            ));
            $data['amount'] = round($baseTripAmount + (float) $data['distance_overage_amount'], 2);
        }

        if (!empty($data['checkin_fuel_level']) && !empty($data['completion_gas_level'])) {
            $fuelOrder = ['empty' => 0, 'quarter' => 1, 'half' => 2, 'three_quarters' => 3, 'full' => 4];
            $checkinLevel = $fuelOrder[$data['checkin_fuel_level']] ?? null;
            $checkoutLevel = $fuelOrder[$data['completion_gas_level']] ?? null;

            if ($checkinLevel !== null && $checkoutLevel !== null && $checkoutLevel < $checkinLevel) {
                $carModel = Car::query()->find($booking->car?->car_id);
                $fuelRate = (float) ($carModel?->fuel_rate_per_liter ?? 0);
                $fuelDiff = $checkinLevel - $checkoutLevel;
                $liters = $fuelDiff * 15;
                $data['fuel_difference_charge'] = round($liters * $fuelRate, 2);
            } else {
                $data['fuel_difference_charge'] = 0;
            }
        }

        if (!empty($data['actual_return_datetime']) && $booking->car) {
            $actualReturn = \Carbon\Carbon::parse($data['actual_return_datetime']);
            $expectedReturn = \Carbon\Carbon::parse($booking->car->rental_end_date)->endOfDay();
            
            if ($actualReturn->gt($expectedReturn)) {
                $lateHours = (int) ceil($expectedReturn->diffInHours($actualReturn));
                $carModel = Car::query()->find($booking->car->car_id);
                $lateRate = (float) ($carModel?->late_fee_per_hour ?? 0);
                $data['late_fee_charge'] = round($lateHours * $lateRate, 2);
            } else {
                $data['late_fee_charge'] = 0;
            }
        }

        if (!empty($data['damage_amount']) && (float) $data['damage_amount'] > 0) {
            $data['damage_status'] = 'pending';
        } else {
            $data['damage_status'] = null;
        }

        $booking->update($data);

        if (!empty($data['damage_amount']) && (float) $data['damage_amount'] > 0 && $data['damage_status'] === 'pending') {
            $acceptUrl = url('/bookings/' . $booking->transaction_id . '/damage/accept');
            $disputeUrl = url('/bookings/' . $booking->transaction_id . '/damage/dispute');
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'plugins/car-rentals::emails.damage-claim',
                    ['booking' => $booking, 'acceptUrl' => $acceptUrl, 'disputeUrl' => $disputeUrl],
                    function ($mail) use ($booking) {
                        $mail->to($booking->customer_email, $booking->customer_name)
                            ->subject('Damage Claim Raised - Booking #' . $booking->booking_number);
                    }
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Damage claim email failed: ' . $e->getMessage());
            }
        }

        if ($booking->deposit_hold_status === 'authorized' && ! empty($settlementAction)) {
            $settlement = app(DepositHoldSettlementService::class)->settle(
                $booking,
                $settlementAction,
                $settlementCaptureAmount
            );

            if (! $settlement['ok']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($settlement['message']);
            }

            $settlementMessage = $settlement['message'] ?? null;

            if (
                Arr::get($data, 'deposit_settlement_action') === 'capture_overage'
                && $overageAmountForSettlement > (float) $settlementCaptureAmount
            ) {
                $remainingOverage = round($overageAmountForSettlement - (float) $settlementCaptureAmount, 2);
                $settlementMessage .= ' ' . trans('plugins/car-rentals::booking.deposit_capture_overage_insufficient_hold', [
                    'captured' => format_price((float) $settlementCaptureAmount, $booking->currency_id),
                    'remaining' => format_price($remainingOverage, $booking->currency_id),
                ]);
            }
        }

        $this->syncDistanceOverageInvoiceItem($booking);

        return $this
            ->httpResponse()
            ->setMessage(
                $settlementMessage
                    ? trans('plugins/car-rentals::booking.completion_details_updated_successfully') . ' ' . $settlementMessage
                    : trans('plugins/car-rentals::booking.completion_details_updated_successfully')
            );
    }

    protected function syncDistanceOverageInvoiceItem(Booking $booking): void
    {
        if (! $booking->invoice()->exists()) {
            return;
        }

        $invoice = $booking->invoice;
        $invoice->loadMissing('items');

        $overageAmount = round((float) ($booking->distance_overage_amount ?? 0), 2);
        $lineItemMarker = '[distance_overage]';

        $existingItem = $invoice->items
            ->first(fn ($item) => $item->description === $lineItemMarker);

        $existingAmount = $existingItem ? (float) $existingItem->amount : 0.0;

        if ($overageAmount > 0) {
            $lineData = [
                'name' => trans('plugins/car-rentals::booking.distance_overage_line_item'),
                'description' => $lineItemMarker,
                'qty' => 1,
                'sub_total' => $overageAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'amount' => $overageAmount,
            ];

            if ($existingItem) {
                $existingItem->update($lineData);
            } else {
                $invoice->items()->create($lineData);
            }
        } elseif ($existingItem) {
            $existingItem->delete();
        }

        $delta = $overageAmount - $existingAmount;

        if (abs($delta) > 0) {
            $invoice->sub_total = round((float) $invoice->sub_total + $delta, 2);
            $invoice->amount = round((float) $invoice->amount + $delta, 2);
            $invoice->save();
        }
    }

    public function uploadPickupPhotos(Request $request, Booking $booking)
    {
        if (!$request->hasFile('pickup_photos')) {
            return response()->json(['error' => 'No photos uploaded.'], 422);
        }

        $uploadedPaths = [];
        foreach ($request->file('pickup_photos') as $photo) {
            $result = RvMedia::handleUpload($photo, 0, 'bookings/pickup-photos');
            if (!$result['error']) {
                $uploadedPaths[] = $result['data']->url;
            }
        }

        $existing = $booking->pickup_photos ?? [];
        $booking->update([
            'pickup_photos' => array_merge($existing, $uploadedPaths),
            'pickup_photos_uploaded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'photos' => $booking->fresh()->pickup_photos,
            'uploaded_at' => $booking->pickup_photos_uploaded_at->format('M d, Y h:i A'),
        ]);
    }
    public function deletePickupPhoto(Request $request, Booking $booking)
    {
        $index = $request->input('index');
        $photos = $booking->pickup_photos ?? [];
    
        if (!isset($photos[$index])) {
            return response()->json(['error' => 'Photo not found.'], 404);
        }
    
        array_splice($photos, $index, 1);
        $booking->update(['pickup_photos' => array_values($photos)]);
    
        return response()->json(['success' => true]);
    }
    public function sendKeyInstructions(Request $request, Booking $booking)
    {
        $request->validate([
            'key_instructions' => 'required|string|min:10',
        ]);

        $booking->update([
            'key_instructions' => $request->key_instructions,
            'key_instructions_sent_at' => now(),
        ]);

        // Send email to customer
        \Illuminate\Support\Facades\Mail::send(
            'plugins/car-rentals::emails.key-instructions',
            ['booking' => $booking, 'instructions' => $request->key_instructions],
            function ($mail) use ($booking) {
                $mail->to($booking->customer_email, $booking->customer_name)
                    ->subject('Your Car Pickup Instructions - Booking #' . $booking->booking_number);
            }
        );

        return redirect()->back()->with('success', 'Key instructions sent to ' . $booking->customer_email . ' successfully!');
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/car-rentals::booking.create'));

        Assets::addScriptsDirectly('vendor/core/plugins/car-rentals/js/booking-create.js');
        Assets::addScriptsDirectly('vendor/core/plugins/car-rentals/js/booking-car-search.js');
        Assets::addScriptsDirectly('vendor/core/plugins/car-rentals/js/customer-autocomplete.js');
        Assets::addStylesDirectly('vendor/core/plugins/car-rentals/css/car-rentals.css');

        return BookingCreateForm::create()->renderForm();
    }

    public function searchCars(Request $request, BaseHttpResponse $response)
    {
        $startDate = CarRentalsHelper::dateFromRequest($request->input('rental_start_date'));
        $endDate = CarRentalsHelper::dateFromRequest($request->input('rental_end_date'));

        if (! $startDate || ! $endDate) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.please_select_dates'));
        }

        $availableCars = Car::query()
            ->active()
            ->get();

        $dateFormat = CarRentalsHelper::getDateFormat();

        $condition = [
            'start_date' => $startDate->format($dateFormat),
            'end_date' => $endDate->format($dateFormat),
        ];

        $cars = [];

        foreach ($availableCars as $car) {
            /**
             * @var Car $car
             */
            if ($car->isAvailableAt($condition)) {
                $cars[] = $car;
            }
        }

        $html = '';
        if (count($cars) > 0) {
            $html = view('plugins/car-rentals::bookings.car-search-results', compact('cars'))->render();
        }

        return $response
            ->setData(compact('html', 'cars'));
    }

    public function searchCustomers(BaseHttpResponse $response)
    {
        $keyword = request()->input('q');

        if (! $keyword) {
            return $response->setData([
                'html' => '',
            ]);
        }

        $customers = Customer::query()
            ->where(function ($query) use ($keyword): void {
                $query
                    ->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%");
            })
            ->limit(10)
            ->get();

        $html = view('plugins/car-rentals::bookings.customer-search-results', compact('customers'))->render();

        return $response->setData(compact('html'));
    }

    public function getCustomer(BaseHttpResponse $response)
    {
        $customerId = request()->input('id');

        if (! $customerId) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.customer_not_found'));
        }

        $customer = Customer::query()->find($customerId);

        if (! $customer) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.customer_not_found'));
        }

        $html = view('plugins/car-rentals::bookings.customer-info', compact('customer'))->render();

        return $response->setData([
            'customer' => $customer,
            'html' => $html,
        ]);
    }

    public function print(Booking $booking)
    {
        $booking->load(['car', 'services', 'customer', 'invoice', 'payment']);

        return view('plugins/car-rentals::bookings.print', compact('booking'));
    }

    public function createCustomer(BaseHttpResponse $response)
    {
        try {
            $validator = validator(request()->all(), [
                'name' => ['required', 'string', 'max:120', 'min:2'],
                'email' => ['required', 'string', 'email', 'max:120', 'unique:cr_customers,email'],
                'phone' => ['required', 'string', 'max:15'],
            ]);

            if ($validator->fails()) {
                return $response
                    ->setError()
                    ->setMessage($validator->errors()->first())
                    ->setData(['errors' => $validator->errors()]);
            }

            $customer = Customer::query()->create([
                'name' => request()->input('name'),
                'email' => request()->input('email'),
                'phone' => request()->input('phone'),
                'status' => BaseStatusEnum::PUBLISHED,
                'password' => bcrypt('123456'), // Default password, customer should change it
            ]);

            $html = view('plugins/car-rentals::bookings.customer-info', compact('customer'))->render();

            return $response->setData([
                'customer' => $customer,
                'html' => $html,
                'message' => trans('plugins/car-rentals::booking.customer_created_successfully'),
            ]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function store(CreateBookingRequest $request, BaseHttpResponse $response)
    {
        $customerId = $request->input('customer_id');
        if (! $customerId || $customerId == '0') {
            $customer = Customer::query()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'status' => BaseStatusEnum::PUBLISHED,
                'password' => bcrypt('123456'), // Default password, customer should change it
            ]);
            $customerId = $customer->id;
        }

        /**
         * @var Car $car
         */
        $car = Car::query()->findOrFail($request->input('car_id'));
        $startDate = Carbon::parse($request->input('rental_start_date'));
        $endDate = Carbon::parse($request->input('rental_end_date'));

        $serviceIds = $request->input('services', []);
        if (is_string($serviceIds)) {
            $serviceIds = json_decode($serviceIds, true) ?: [];
        }

        $couponCode = $request->input('coupon_code');
        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $startDate,
            $endDate,
            $serviceIds,
            [],
            $couponCode,
            null
        );

        $booking = new Booking();
        $booking->fill([
            'status' => $request->input('status'),
            'customer_id' => $customerId,
            'customer_name' => $request->input('name'),
            'customer_email' => $request->input('email'),
            'customer_phone' => $request->input('phone'),
            'note' => $request->input('note'),
            'booking_number' => Booking::generateUniqueBookingNumber(),
            'vendor_id' => $car->author_id,
        ]);

        $booking->transaction_id = Str::upper(Str::random(32));

        $couponAmount = (float) ($quoteData['coupon_amount'] ?? 0);
        $manualCouponAmount = $request->input('coupon_amount');
        if ($manualCouponAmount !== null && $manualCouponAmount !== '' && is_numeric($manualCouponAmount) && $manualCouponAmount >= 0) {
            $couponCap = (float) $quoteData['subtotal'] + (float) $quoteData['tax_amount'];
            $couponAmount = min((float) $manualCouponAmount, $couponCap);
        }

        $totalAmount = ((float) $quoteData['subtotal'] + (float) $quoteData['tax_amount'] - $couponAmount + (float) $quoteData['fee_amount']);
        $finalPayableAmount = $totalAmount + (float) $quoteData['deposit_amount'];

        if ($couponCode && $couponAmount > 0) {
            $booking->coupon_code = $couponCode;
        }

        $booking->coupon_amount = $couponAmount;
        $booking->amount = $finalPayableAmount;
        $booking->sub_total = (float) $quoteData['subtotal'];
        $booking->tax_amount = (float) $quoteData['tax_amount'];
        $booking->fee_name = (string) $quoteData['fee_name'];
        $booking->fee_value = (float) $quoteData['fee_value'];
        $booking->fee_amount = (float) $quoteData['fee_amount'];
        $booking->deposit_base_amount = (float) $quoteData['deposit_base_amount'];
        $booking->deposit_amount = (float) $quoteData['deposit_amount'];
        $booking->deposit_type = (string) $quoteData['deposit_type'];
        $booking->deposit_rate = (float) $quoteData['deposit_rate'];
        $booking->deposit_risk_multiplier = (float) Arr::get($quoteData, 'deposit_risk.multiplier', 1);
        $booking->deposit_risk_level = (string) Arr::get($quoteData, 'deposit_risk.risk_level', 'low');
        $booking->deposit_risk_reasons = Arr::get($quoteData, 'deposit_risk.reasons', []);
        $booking->price_snapshot = [
            'rental_days' => (int) ($quoteData['rental_days'] ?? 1),
            'base_rental_amount' => (float) ($quoteData['base_rental_amount'] ?? 0),
            'policy_discount_amount' => (float) ($quoteData['policy_discount_amount'] ?? 0),
            'policy_discount_pre_cap_amount' => (float) ($quoteData['policy_discount_pre_cap_amount'] ?? 0),
            'policy_discount_capped' => (bool) ($quoteData['policy_discount_capped'] ?? false),
            'policy_discount_cap_percent' => $quoteData['policy_discount_cap_percent'] !== null
                ? (float) $quoteData['policy_discount_cap_percent']
                : null,
            'policy_discount_source' => (string) ($quoteData['policy_discount_source'] ?? ''),
        ];
        $booking->distance_unit = (string) ($quoteData['distance_unit'] ?? 'km');
        $booking->start_mileage = $car->mileage !== null ? (int) $car->mileage : null;
        $booking->start_mileage_snapshot = $car->mileage !== null ? (int) $car->mileage : null;
        $booking->included_distance_limit = $quoteData['included_distance_limit'] !== null
            ? (int) $quoteData['included_distance_limit']
            : null;
        $booking->distance_overage_billing_mode = (string) ($quoteData['distance_overage_billing_mode'] ?? 'end_of_trip');
        $booking->extra_distance_unit_price = (float) ($quoteData['extra_distance_unit_price'] ?? 0);
        $booking->currency_id = $car->currency_id;
        $booking->save();

        if ($serviceIds) {
            $booking->services()->attach($serviceIds);
        }

        BookingCar::query()->create([
            'car_id' => $car->getKey(),
            'car_name' => $car->name,
            'car_image' => Arr::first($car->images),
            'booking_id' => $booking->getKey(),
            'price' => (float) $quoteData['rental_amount'],
            'currency_id' => get_application_currency()->id,
            'rental_start_date' => $startDate->format('Y-m-d'),
            'rental_end_date' => $endDate->format('Y-m-d'),
            'pickup_city_id' => null,  // Will be customer-selected
            'return_city_id' => null,  // Will be customer-selected
        ]);

        // Handle payment
        if ($request->input('payment_method')) {
            $paymentData = [
                'amount' => $booking->amount,
                'currency' => $booking->currency->title,
                'type' => 'direct',
                'charge_id' => Str::upper(Str::random(10)),
                'order_id' => [$booking->id],
                'customer_id' => $booking->customer_id,
                'customer_type' => Customer::class,
                'payment_channel' => $request->input('payment_method'),
                'status' => $request->input('payment_status'),
            ];

            $payment = null;

            switch ($request->input('payment_method')) {
                case PaymentMethodEnum::COD:
                    $codPaymentService = app(CodPaymentService::class);
                    $codPaymentService->execute($paymentData);

                    break;

                case PaymentMethodEnum::BANK_TRANSFER:
                    $bankTransferPaymentService = app(BankTransferPaymentService::class);
                    $bankTransferPaymentService->execute($paymentData);

                    break;

                default:
                    $payment = PaymentHelper::storeLocalPayment($paymentData);

                    break;
            }

            // Get the payment record and associate it with the booking
            if (! $payment) {
                $payment = Payment::query()
                    ->where('charge_id', $paymentData['charge_id'])
                    ->where('order_id', $booking->id)
                    ->first();
            }

            if ($payment) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }
        }

        BookingCreated::dispatch($booking);

        return $response
            ->setPreviousUrl(route('car-rentals.bookings.index'))
            ->setNextUrl(route('car-rentals.bookings.edit', $booking->id))
            ->withCreatedSuccessMessage();
    }
}
