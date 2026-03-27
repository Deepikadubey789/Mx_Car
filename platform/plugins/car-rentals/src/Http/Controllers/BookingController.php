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
use Botble\CarRentals\Models\Service;
use Botble\CarRentals\Services\CouponService;
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

        $booking->update($data);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::booking.completion_details_updated_successfully'));
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

        $rentalDays = $startDate->diffInDays($endDate) ?: 1;
        $carPrice = $car->getCarRentalPrice($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        $serviceIds = $request->input('services', []);
        if (is_string($serviceIds)) {
            $serviceIds = json_decode($serviceIds, true) ?: [];
        }

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

        // Calculate amount
        $amount = $carPrice;
        $serviceAmount = 0;

        if ($serviceIds) {
            $services = Service::query()
                ->wherePublished()
                ->whereIn('id', $serviceIds)
                ->get();

            foreach ($services as $service) {
                if ($service->price_type && $service->price_type->getValue() === 'per_day') {
                    $serviceAmount += $service->price * $rentalDays;
                } else {
                    $serviceAmount += $service->price;
                }
            }
            $amount += $serviceAmount;
        }

        // Calculate tax
        $taxAmount = 0;
        if ($car->tax && $car->tax->percentage) {
            $taxAmount = $amount * $car->tax->percentage / 100;
        }

        // Process coupon if provided
        $couponAmount = 0;
        $couponCode = $request->input('coupon_code');
        if ($couponCode) {
            $couponService = app(CouponService::class);
            $coupon = $couponService->getCouponByCode($couponCode);

            if ($coupon) {
                $couponAmount = $couponService->getDiscountAmount(
                    $coupon->type->getValue(),
                    $coupon->value,
                    $amount + $taxAmount
                );

                // Use manual coupon amount if provided and valid
                $manualCouponAmount = $request->input('coupon_amount');
                if ($manualCouponAmount && is_numeric($manualCouponAmount) && $manualCouponAmount >= 0) {
                    $couponAmount = min($manualCouponAmount, $amount + $taxAmount);
                }

                $booking->coupon_code = $couponCode;
                $booking->coupon_amount = $couponAmount;
            }
        } elseif ($request->input('coupon_amount')) {
            // Allow manual coupon amount without code for admin flexibility
            $manualCouponAmount = $request->input('coupon_amount');
            if (is_numeric($manualCouponAmount) && $manualCouponAmount >= 0) {
                $couponAmount = min($manualCouponAmount, $amount + $taxAmount);
                $booking->coupon_amount = $couponAmount;
            }
        }

        $booking->amount = $amount + $taxAmount - $couponAmount;
        $booking->sub_total = $amount;
        $booking->tax_amount = $taxAmount;
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
            'price' => $carPrice,
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
