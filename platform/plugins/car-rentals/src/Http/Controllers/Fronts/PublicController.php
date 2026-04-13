<?php

namespace Botble\CarRentals\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Events\BookingCreated;
use Botble\CarRentals\Facades\BookingHelper;
use Botble\CarRentals\Facades\CarListHelper;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\Fronts\CheckoutForm;
use Botble\CarRentals\Http\Requests\Fronts\BookingRequest;
use Botble\CarRentals\Http\Requests\Fronts\CheckoutRequest;
use Botble\CarRentals\Http\Requests\Fronts\ReviewRequest;
use Botble\CarRentals\Http\Resources\LocationResource;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarMake;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\CarTag;
use Botble\CarRentals\Models\Currency;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Models\Service;
use Botble\CarRentals\Models\GuestProtectionPlan;
use Botble\CarRentals\Services\BookingService;
use Botble\CarRentals\Services\PriceLockService;
use Botble\CarRentals\Services\PricingQuoteService;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Botble\CarRentals\Repositories\Interfaces\CarInterface;
use Botble\Location\Repositories\Eloquent\CityRepository;
use Botble\Location\Repositories\Eloquent\StateRepository;

class PublicController extends BaseController
{
  public function getCars(Request $request)
    {
        SeoHelper::setTitle(__('Cars'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Cars'), route('public.cars'));

        // 1. Unified Keyword Search
        if ($keyword = $request->input('keyword')) {
            Car::addGlobalScope('keyword_search', function ($builder) use ($keyword) {
                $words = array_filter(explode(' ', $keyword));
                foreach ($words as $word) {
                    $builder->where('cr_cars.name', 'LIKE', '%' . trim($word) . '%');
                }
            });
            // Strip from request so core doesn't duplicate the filter
            $request->query->remove('keyword');
            $request->query->remove('q');
        }

        // 2. Unified Advanced Location Search
        if ($location = $request->input('location')) {
            Car::addGlobalScope('location_search', function ($builder) use ($location) {
                $searchTerm = trim(explode(',', $location)[0]);

                $builder->where(function ($query) use ($searchTerm) {
                    $query->whereHas('country', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('state', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('city', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhere('cr_cars.address', 'LIKE', '%' . $searchTerm . '%');
                });
            });

            // CRITICAL FIX: Destroy URL parameters so Botble's core doesn't run a conflicting blank search!
            $request->query->remove('city_id');
            $request->query->remove('location');
            $request->request->remove('city_id');
            $request->request->remove('location');
        }

        $requestQuery = CarListHelper::getCarFilters($request->input());

        $with = [
            'slugable', 'transmission', 'fuel', 'city', 'state', 'country', 'make', 'author', 'currency'
        ];

        $sortBy = $requestQuery['sort_by'] ?? 'recently_added';
        $perPage = $requestQuery['per_page'] ?? CarRentalsHelper::getCarsPerPage();
        $currentPage = $requestQuery['page'] ?? 1;

        $cars = app(\Botble\CarRentals\Repositories\Interfaces\CarInterface::class)->getCars(
            $requestQuery,
            [
                'with' => $with,
                'order_by' => $sortBy,
                'paginate' => [
                    'per_page' => $perPage ?: 10,
                    'current_paged' => $currentPage ?: 1,
                ],
            ]
        );

        $cars->loadCount('reviews')->loadAvg('reviews', 'star');

        return Theme::scope('car-rentals.cars', compact('cars'), 'plugins/car-rentals::themes.cars')->render();
    }

    public function getCar(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Car::class));

        abort_unless($slug, 404);

        $version = get_cms_version();

        Theme::asset()
            ->add('front-car-rentals-css', 'vendor/core/plugins/car-rentals/css/front-theme.css', version: $version);

        $car = $slug->reference;

        abort_unless($car, 404);

        $car
            ->loadMissing(['tags', 'make', 'amenities.category', 'city', 'state', 'country'])
            ->loadAvg('reviews', 'star')
            ->loadSum('reviews', 'star')
            ->loadCount('reviews');

        abort_if($car->status->getValue() !== CarStatusEnum::AVAILABLE, 404);

        $enabledPostApproval = CarRentalsHelper::isEnabledPostApproval();

        if ($enabledPostApproval) {
            abort_if($car->moderation_status->getValue() !== ModerationStatusEnum::APPROVED, 404);
        }

        $reviews = CarReview::query()
            ->with('customer')
            ->where('car_id', $car->getKey())
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->paginate(CarRentalsHelper::getCarsPerPage());

        // --- FIX: Fetch global Guest Protection Plans instead of old vendor insurances ---
        $guestProtectionPlans = GuestProtectionPlan::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();

        SeoHelper::setTitle($car->name)->setDescription(Str::words($car->description ?? '', 120));

        $meta = new SeoOpenGraph();

        $meta->setDescription($car->description ?? '');
        $meta->setUrl($car->url);
        $meta->setTitle($car->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Cars'), route('public.cars'))
            ->add($car->name, $car->url);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this car'), route('car-rentals.cars.edit', $car->getKey()));
        }

        // Notice we changed 'insurances' to 'guestProtectionPlans'
        return Theme::scope('car-rentals.car', compact('car', 'reviews', 'guestProtectionPlans'), 'plugins/car-rentals::themes.car')->render();
    }

    public function getService(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Service::class));

        abort_unless($slug, 404);

        $service = $slug->reference;

        abort_if($service->status->getValue() !== BaseStatusEnum::PUBLISHED, 404);

        SeoHelper::setTitle($service->name)->setDescription(Str::words($service->description ?? '', 120));

        $meta = new SeoOpenGraph();

        $meta->setDescription($service->description ?? '');
        $meta->setUrl($service->url);
        $meta->setTitle($service->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Services'))
            ->add($service->name, $service->url);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this car'), route('car-rentals.services.edit', $service->getKey()));
        }

        return Theme::scope('car-rentals.service', compact('service'), 'plugins/car-rentals::themes.service')->render();
    }

    public function postBooking(BookingRequest $request)
    {
        if (! CarRentalsHelper::isRentalBookingEnabled()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Car rental booking is not available.'))
                ->withInput();
        }

        $car = Car::query()
            ->findOrFail($request->input('car_id'));

        if ($car->is_for_sale) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('This car is listed for sale only and cannot be rented.'))
                ->withInput();
        }

        $token = md5(Str::random(40));

        session([
            $token => $request->except(['_token']),
            'checkout_token' => $token,
        ]);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.booking.form', $token));
    }

    public function getBooking(string $token)
    {
        if (! CarRentalsHelper::isRentalBookingEnabled()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Car rental booking is not available.'))
                ->withInput();
        }

        SeoHelper::setTitle(__('Booking'));

        OptimizerHelper::disable();

        $sessionData = [];
        if (session()->has($token)) {
            $sessionData = session($token);
        }

        $carId = Arr::get($sessionData, 'car_id');

        if (! $carId) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        $car = Car::query()
            ->with(['tax', 'city', 'state', 'country'])
            ->whereKey($carId)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        if ($car->is_for_sale) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('This car is listed for sale only and cannot be rented.'))
                ->withInput();
        }

        $startDate = ! empty($sessionData['rental_start_date']) ? CarRentalsHelper::dateFromRequest($sessionData['rental_start_date']) : null;
        $endDate = ! empty($sessionData['rental_end_date']) ? CarRentalsHelper::dateFromRequest($sessionData['rental_end_date']) : null;
        $startTime = Arr::get($sessionData, 'rental_start_time', '09:00');
        $endTime = Arr::get($sessionData, 'rental_end_time', '09:00');

        if ($startDate && $startTime) {
            $startDate = Carbon::parse($startDate->toDateString() . ' ' . $startTime);
        }
        if ($endDate && $endTime) {
            $endDate = Carbon::parse($endDate->toDateString() . ' ' . $endTime);
        }

        if (! $car->isAvailableAt(['start_date' => $startDate, 'end_date' => $endDate])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking from :start_date to :end_date!',
                    [
                        'start_date' => $startDate ? $startDate->toDateString() : 'N/A',
                        'end_date' => $endDate ? $endDate->toDateString() : 'N/A',
                    ]
                ))
                ->withInput();
        }

        $pricing = $this->prepareCheckoutBookingPricing($sessionData, $car, $startDate, $endDate);

        $data = [
            'car' => $car,
            'amount' => $pricing['amount'],
            'totalAmount' => $pricing['totalAmount'],
            'taxTitle' => $pricing['taxTitle'],
            'taxAmount' => $pricing['taxAmount'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'couponCode' => $pricing['couponCode'],
            'couponAmount' => $pricing['discountAmount'],
            'token' => $token,
            'rentalCarAmount' => $pricing['rentalCarAmount'],
            'serviceIds' => $pricing['serviceIds'],
            'services' => $pricing['services'],
            
            // --- FIX: Removed 'insurances' and added Guest Plans ---
            'guest_protection_plan' => $pricing['guest_protection_plan'] ?? null,
            'guest_protection_fee' => $pricing['guest_protection_fee'] ?? 0,
            
            'feeName' => $pricing['feeName'],
            'feeValue' => $pricing['feeValue'],
            'feeAmount' => $pricing['feeAmount'],
            'depositBaseAmount' => $pricing['depositBaseAmount'],
            'depositAmount' => $pricing['depositAmount'],
            'depositType' => $pricing['depositType'],
            'depositRate' => $pricing['depositRate'],
            'depositRiskLevel' => $pricing['depositRisk']['risk_level'],
            'depositRiskMultiplier' => (float) $pricing['depositRisk']['multiplier'],
            'depositRiskReasons' => $pricing['depositRisk']['reasons'],
            'finalPayableAmount' => $pricing['finalPayableAmount'],
            'priceLockExpiresAt' => $pricing['priceLockExpiresAt'],
            'priceLockExpiredMessage' => $pricing['priceLockExpiredMessage'],
        ];

        return view(
            'plugins/car-rentals::checkouts.index',
            [
                'checkoutForm' => CheckoutForm::createFromArray($data),
                ...$data,
            ],
        );
    }

    public function updateGetBooking(string $token)
    {
        $sessionData = [];
        if (session()->has($token)) {
            $sessionData = session($token);
        }

        $carId = Arr::get($sessionData, 'car_id');

        if (! $carId) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        $car = Car::query()
            ->with(['tax', 'city', 'state', 'country'])
            ->whereKey($carId)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        $startDate = ! empty($sessionData['rental_start_date']) ? CarRentalsHelper::dateFromRequest($sessionData['rental_start_date']) : null;
        $endDate = ! empty($sessionData['rental_end_date']) ? CarRentalsHelper::dateFromRequest($sessionData['rental_end_date']) : null;
        $startTime = Arr::get($sessionData, 'rental_start_time', '09:00');
        $endTime = Arr::get($sessionData, 'rental_end_time', '09:00');

        if ($startDate && $startTime) {
            $startDate = Carbon::parse($startDate->toDateString() . ' ' . $startTime);
        }
        if ($endDate && $endTime) {
            $endDate = Carbon::parse($endDate->toDateString() . ' ' . $endTime);
        }

        if (! $car->isAvailableAt(['start_date' => $startDate, 'end_date' => $endDate])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking from :start_date to :end_date!',
                    [
                        'start_date' => $startDate ? $startDate->toDateString() : 'N/A',
                        'end_date' => $endDate ? $endDate->toDateString() : 'N/A',
                    ]
                ))
                ->withInput();
        }

        $pricing = $this->prepareCheckoutBookingPricing($sessionData, $car, $startDate, $endDate);

        $displayToken = session('checkout_token') ?: $token;

        $viewData = [
            'car' => $car,
            'amount' => $pricing['amount'],
            'totalAmount' => $pricing['totalAmount'],
            'taxTitle' => $pricing['taxTitle'],
            'taxAmount' => $pricing['taxAmount'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'couponCode' => $pricing['couponCode'],
            'couponAmount' => $pricing['discountAmount'],
            'token' => $displayToken,
            'services' => $pricing['services'],
            
            // --- FIX: Removed 'insurances' and added Guest Plans ---
            'guest_protection_plan' => $pricing['guest_protection_plan'] ?? null,
            'guest_protection_fee' => $pricing['guest_protection_fee'] ?? 0,
            
            'rentalCarAmount' => $pricing['rentalCarAmount'],
            'feeName' => $pricing['feeName'],
            'feeValue' => $pricing['feeValue'],
            'feeAmount' => $pricing['feeAmount'],
            'depositAmount' => $pricing['depositAmount'],
            'depositBaseAmount' => $pricing['depositBaseAmount'],
            'depositType' => $pricing['depositType'],
            'depositRate' => $pricing['depositRate'],
            'depositRiskLevel' => $pricing['depositRisk']['risk_level'],
            'depositRiskMultiplier' => (float) $pricing['depositRisk']['multiplier'],
            'depositRiskReasons' => $pricing['depositRisk']['reasons'],
            'finalPayableAmount' => $pricing['finalPayableAmount'],
            'priceLockExpiresAt' => $pricing['priceLockExpiresAt'],
            'priceLockExpiredMessage' => $pricing['priceLockExpiredMessage'],
        ];

        $response = $this
            ->httpResponse()
            ->setData(view('plugins/car-rentals::checkouts.partials.booking-information', $viewData)->render());

        if ($pricing['lockWasRefreshed']) {
            return $response
                ->setError()
                ->setMessage($pricing['priceLockExpiredMessage']);
        }

        return $response;
    }

    public function postCheckout(CheckoutRequest $request)
    {
        $sessionData = BookingHelper::getCheckoutData();
        $priceLockService = app(PriceLockService::class);
        $priceLockExpiredMessage = $priceLockService->getExpiredMessage();

        if (! $carId = Arr::get($sessionData, 'car_id')) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        $car = Car::query()
            ->with('tax')
            ->whereKey($carId)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking!',
                ))
                ->withInput();
        }

        $startDate = $sessionData['rental_start_date'] ? CarRentalsHelper::dateFromRequest($sessionData['rental_start_date']) : null;
        $endDate = $sessionData['rental_end_date'] ? CarRentalsHelper::dateFromRequest($sessionData['rental_end_date']) : null;
        $startTime = Arr::get($sessionData, 'rental_start_time', '09:00');
        $endTime = Arr::get($sessionData, 'rental_end_time', '09:00');

        $serviceIds = Arr::get($sessionData, 'service_ids', []);
        
        // --- FIX: Get the single Guest Plan ID instead of the old array ---
        $guestProtectionPlanId = Arr::get($sessionData, 'guest_protection_plan_id');
        $guestProtectionPlanId = $guestProtectionPlanId ? (int) $guestProtectionPlanId : null;
        
        $couponCode = Arr::get($sessionData, 'coupon_code');

        if (! $car->isAvailableAt(['start_date' => $startDate, 'end_date' => $endDate])) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__(
                    'This car is not available for booking from :start_date to :end_date!',
                    ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]
                ))
                ->withInput();
        }

        if ($request->input('is_register') == 1) {
            $request->validate([
                'customer_email' => ['required', 'max:60', 'min:6', 'email', 'unique:cr_customers,email'],
            ]);

            $customer = Customer::query()->create([
                'name' => BaseHelper::clean($request->input('customer_name')),
                'email' => BaseHelper::clean($request->input('customer_email')),
                'phone' => BaseHelper::clean($request->input('customer_phone')),
                'password' => Hash::make($request->input('password')),
            ]);

            Auth::guard('customer')->loginUsingId($customer->getKey());
        }

        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $startDate,
            $endDate,
            $serviceIds,
            $guestProtectionPlanId, // FIX: Pass the single ID
            $couponCode,
            Auth::guard('customer')->user()
        );

        $services = $quoteData['services'];
        $rentalCarAmount = (float) $quoteData['rental_amount'];
        $amount = (float) $quoteData['subtotal'];
        $taxAmount = (float) $quoteData['tax_amount'];
        $discountAmount = (float) $quoteData['coupon_amount'];
        $feeName = (string) $quoteData['fee_name'];
        $feeValue = (float) $quoteData['fee_value'];
        $feeAmount = (float) $quoteData['fee_amount'];
        $depositType = (string) $quoteData['deposit_type'];
        $depositRate = (float) $quoteData['deposit_rate'];
        $baseDepositAmount = (float) $quoteData['deposit_base_amount'];
        $depositRisk = $quoteData['deposit_risk'];
        $depositAmount = (float) $quoteData['deposit_amount'];
        $finalPayableAmount = (float) $quoteData['final_payable_amount'];

        BookingHelper::saveCheckoutData([
            'coupon_amount' => $discountAmount,
        ]);

        $booking = new Booking($request->validated());

       // --- NEW: Snapshot the Guest and Host Protection Plans directly onto the booking! ---
        // Safely check if the plan actually loaded in the quote before saving the ID
        $booking->guest_protection_plan_id = $quoteData['guest_protection_plan'] ? $quoteData['guest_protection_plan']->id : null;
        $booking->guest_protection_fee = (float) $quoteData['guest_protection_fee'];
        $booking->guest_deductible_amount = (float) $quoteData['guest_deductible_amount'];
        
        $hostPlan = $car->hostProtectionPlan;
        if ($hostPlan && $hostPlan->id) {
            $booking->host_protection_plan_id = $hostPlan->id;
            $booking->host_revenue_share_percentage = $hostPlan->revenue_share_percentage;
            $booking->host_deductible_amount = $hostPlan->deductible_amount;
        }
        // -----------------------------------------------------------------------------------

        $booking->sub_total = $amount;
        $booking->coupon_code = $couponCode;
        $booking->coupon_amount = $discountAmount;
        $booking->amount = $finalPayableAmount;
        $booking->tax_amount = $taxAmount;
        $booking->fee_name = $feeName;
        $booking->fee_value = $feeValue;
        $booking->fee_amount = $feeAmount;
        $booking->deposit_base_amount = $baseDepositAmount;
        $booking->deposit_amount = $depositAmount;
        $booking->deposit_type = $depositType;
        $booking->deposit_rate = $depositRate;
        $booking->deposit_risk_multiplier = (float) $depositRisk['multiplier'];
        $booking->deposit_risk_level = $depositRisk['risk_level'];
        $booking->deposit_risk_reasons = $depositRisk['reasons'];
        $booking->deposit_hold_status = $depositAmount > 0 ? 'pending_authorization' : null;
        $booking->deposit_hold_amount = $depositAmount;
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
        $booking->included_distance_limit = $quoteData['included_distance_limit'] !== null
            ? (int) $quoteData['included_distance_limit']
            : null;
        $booking->start_mileage_snapshot = $car->mileage !== null ? (int) $car->mileage : null;
        $booking->distance_overage_billing_mode = (string) ($quoteData['distance_overage_billing_mode'] ?? 'end_of_trip');
        $booking->extra_distance_unit_price = (float) ($quoteData['extra_distance_unit_price'] ?? 0);
        $booking->currency_id = $request->input('currency_id', strtoupper(get_application_currency()->id));
        $booking->booking_number = Booking::generateUniqueBookingNumber();
        $booking->transaction_id = Str::upper(Str::random(32));
        $booking->vendor_id = $car->author_type == Customer::class && $car->author->is_vendor ? $car->author_id : null;

        if (Auth::guard('customer')->check()) {
            $booking->customer_id = Auth::guard('customer')->id();
        }

        $booking->save();

        session()->put('booking_transaction_id', $booking->transaction_id);

        $rentalStartDateTime = Carbon::parse($startDate->toDateString() . ' ' . $startTime);
        $rentalEndDateTime = Carbon::parse($endDate->toDateString() . ' ' . $endTime);

        BookingCar::query()->create([
            'booking_id' => $booking->id,
            'car_id' => $car->id,
            'car_image' => $car->image,
            'car_name' => $car->name,
            'rental_start_date' => $rentalStartDateTime,
            'rental_end_date' => $rentalEndDateTime,
            'price' => $rentalCarAmount,
            'pickup_city_id' => null,
            'return_city_id' => null,
            'currency_id' => $request->input('currency_id', strtoupper(get_application_currency()->id)),
        ]);

        if ($services->isNotEmpty()) {
            $booking->services()->attach($services->pluck('id')->all());
        }

        // --- FIX: Removed the old $booking->insurances()->attach() method completely! ---

        $request->merge([
            'order_id' => $booking->getKey(),
        ]);

        $data = [
            'error' => false,
            'message' => false,
            'amount' => $booking->amount,
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        if (is_plugin_active('payment')) {
            session()->put('selected_payment_method', $data['type']);

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            switch ($request->input('payment_method')) {
                case PaymentMethodEnum::COD:
                    $codPaymentService = app(CodPaymentService::class);
                    $data['charge_id'] = $codPaymentService->execute($paymentData);
                    $data['message'] = trans('plugins/payment::payment.payment_pending');
                    break;
                case PaymentMethodEnum::BANK_TRANSFER:
                    $bankTransferPaymentService = app(BankTransferPaymentService::class);
                    $data['charge_id'] = $bankTransferPaymentService->execute($paymentData);
                    $data['message'] = trans('plugins/payment::payment.payment_pending');
                    break;
                default:
                    $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);
                    break;
            }

            if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
                return $this
                    ->httpResponse()
                    ->setError($data['error'])
                    ->setNextUrl($checkoutUrl)
                    ->setData(['checkoutUrl' => $checkoutUrl])
                    ->withInput()
                    ->setMessage($data['message']);
            }

            if ($data['error'] || ! $data['charge_id']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->withInput()
                    ->setMessage($data['message'] ?: __('Checkout error!'));
            }

            $bookingService = new BookingService();
            $bookingService->processBooking($booking->getKey(), $data['charge_id']);
            BookingCreated::dispatch($booking);
            $redirectUrl = PaymentHelper::getRedirectURL();
        } else {
            BookingCreated::dispatch($booking);
            $redirectUrl = route('public.booking.information', $booking->transaction_id);
        }

        if ($token = $request->input('token')) {
            session()->forget($token);
            session()->forget('checkout_token');
        }

        return $this
            ->httpResponse()
            ->setNextUrl($redirectUrl)
            ->setMessage(__('Booking successfully!'));
    }

    public function getCheckoutSuccess(string $transactionId)
    {
        $booking = Booking::query()
            ->where('transaction_id', $transactionId)
            ->latest('id')
            ->first();

        abort_unless($booking, 404);

        if (is_plugin_active('payment') && (float) $booking->amount && ! $booking->payment_id) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        }

        return view('plugins/car-rentals::checkouts.thank-you', compact('booking'));
    }

    public function estimateBooking(Request $request)
    {
        $request->validate([
            'car_id' => ['required', 'exists:cr_cars,id'],
            'rental_start_date' => ['required', 'string', 'date'],
            'rental_start_time' => ['nullable', 'string', 'date_format:H:i'],
            'rental_end_date' => ['required', 'string', 'date'],
            'rental_end_time' => ['nullable', 'string', 'date_format:H:i'],
            'service_ids' => ['nullable', 'array'],
            // --- FIX: Now expects a single ID instead of an array ---
            'guest_protection_plan_id' => ['nullable', 'integer'], 
        ]);

        $car = Car::query()
            ->whereKey($request->input('car_id'))
            ->first();

        $startDate = $request->input('rental_start_date') ? CarRentalsHelper::dateFromRequest($request->input('rental_start_date')) : null;
        $endDate = $request->input('rental_end_date') ? CarRentalsHelper::dateFromRequest($request->input('rental_end_date')) : null;

        // --- FIX: Pass the single guest_protection_plan_id as an integer ---
        $guestProtectionPlanId = $request->input('guest_protection_plan_id') ? (int) $request->input('guest_protection_plan_id') : null;

        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $startDate,
            $endDate,
            $request->input('service_ids', []),
            $guestProtectionPlanId, // Changed from insurance_ids array
            null,
            Auth::guard('customer')->user()
        );

        $data = [
            'subtotal' => (float) $quoteData['subtotal'],
            'total' => (float) $quoteData['final_payable_amount'],
            'tax' => (float) $quoteData['tax_amount'],
            'taxInfo' => (string) $quoteData['tax_title'],
            'discount' => (float) $quoteData['coupon_amount'],
            'currencyId' => $car->currency_id,
            'depositAmount' => (float) $quoteData['deposit_amount'],
            'feeAmount' => (float) $quoteData['fee_amount'],
            'rentalDays' => (int) $quoteData['rental_days'],
            'baseRentalAmount' => (float) $quoteData['base_rental_amount'],
            'rentalAmount' => (float) $quoteData['rental_amount'],
            'policyDiscountAmount' => (float) $quoteData['policy_discount_amount'],
            'policyDiscountSource' => (string) ($quoteData['policy_discount_source'] ?? ''),
            'serviceAmount' => (float) $quoteData['service_amount'],
            
            // --- FIX: Pass the new Guest Protection Fee to the estimate view ---
            'guestProtectionFee' => (float) $quoteData['guest_protection_fee'],
            
            'feeName' => (string) ($quoteData['fee_name'] ?? ''),
            'depositType' => (string) ($quoteData['deposit_type'] ?? 'percentage'),
            'depositRate' => (float) ($quoteData['deposit_rate'] ?? 0),
            'depositBaseAmount' => (float) ($quoteData['deposit_base_amount'] ?? 0),
            'includedDistanceLimit' => $quoteData['included_distance_limit'] !== null
                ? (int) $quoteData['included_distance_limit']
                : null,
            'distanceUnit' => (string) ($quoteData['distance_unit'] ?? 'km'),
            'extraDistanceUnitPrice' => (float) ($quoteData['extra_distance_unit_price'] ?? 0),
            'distanceOverageBillingMode' => (string) ($quoteData['distance_overage_billing_mode'] ?? 'end_of_trip'),
        ];

        return $this
            ->httpResponse()
            ->setData(view('plugins/car-rentals::cars.partials.booking-form-estimate', [...$data])->render());
    }

   public function postCarReviews(ReviewRequest $request)
    {
        abort_unless(CarRentalsHelper::isEnabledCarReviews(), 404);

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Please login to add a review!'));
        }

        $car = Car::query()
            ->whereKey($request->input('car_id'))
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Car not found!'));
        }

        $bookingId = $request->input('booking_id');

        // Build the query to check for existing reviews
        $query = CarReview::query()
            ->where('car_id', $car->getKey())
            ->where('customer_id', $customer->getKey());

        // If a booking_id was provided (from the booking info page), check against that specific booking
        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }

        if ($query->exists()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('You have already reviewed this car for this trip!'));
        }

        // Save the review
        CarReview::query()->create([
            ...$request->validated(),
            'booking_id'     => $bookingId, // Save the booking ID
            'customer_name'  => $customer->name,
            'customer_email' => $customer->email,
            'status'         => BaseStatusEnum::PUBLISHED,
        ]);

        return $this
            ->httpResponse()
            ->setMessage(__('Thank you! Your review has been added successfully.'));
    }
   public function ajaxGetCars(Request $request)
    {
        // 1. Unified Keyword Search
        if ($keyword = $request->input('keyword')) {
            Car::addGlobalScope('keyword_search', function ($builder) use ($keyword) {
                $words = array_filter(explode(' ', $keyword));
                foreach ($words as $word) {
                    $builder->where('cr_cars.name', 'LIKE', '%' . trim($word) . '%');
                }
            });
            $request->query->remove('keyword');
            $request->query->remove('q');
        }

        // 2. Unified Advanced Location Search
        if ($location = $request->input('location')) {
            Car::addGlobalScope('location_search', function ($builder) use ($location) {
                $searchTerm = trim(explode(',', $location)[0]);

                $builder->where(function ($query) use ($searchTerm) {
                    $query->whereHas('country', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('state', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('city', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->orWhere('cr_cars.address', 'LIKE', '%' . $searchTerm . '%');
                });
            });

            // CRITICAL FIX: Destroy URL parameters
            $request->query->remove('city_id');
            $request->query->remove('location');
            $request->request->remove('city_id');
            $request->request->remove('location');
        }

        $requestQuery = CarListHelper::getCarFilters($request->input());

        $with = [
            'slugable', 'transmission', 'fuel', 'city', 'state', 'country', 'make'
        ];

        $sortBy = $requestQuery['sort_by'] ?? 'recently_added';
        $perPage = $requestQuery['per_page'] ?? Arr::first(CarListHelper::getPerPageParams());
        $currentPage = $requestQuery['page'] ?? 1;

        $cars = app(\Botble\CarRentals\Repositories\Interfaces\CarInterface::class)->getCars(
            $requestQuery,
            [
                'with' => $with,
                'order_by' => $sortBy,
                'paginate' => [
                    'per_page' => $perPage ?: 10,
                    'current_paged' => $currentPage ?: 1,
                ],
            ]
        );

        $additional['total'] = $cars->total();
        $message = $additional['total'] ? __(':total items found', ['total' => $cars->total()]) : __('No results found');
        $additional['message'] = $message;

        $carsView = Theme::getThemeNamespace('views.car-rentals.car-list.partials.car-items');
        if (! view()->exists($carsView)) {
            $carsView = 'plugins/car-rentals::themes.includes.car-items';
        }

        $filtersData['cars'] = $cars;
        $filtersView = Theme::getThemeNamespace('views.car-rentals.car-list.partials.filters');

        if (view()->exists($filtersView)) {
            $additional['filters_html'] = view($filtersView, $filtersData)->render();
        }

        return $this
            ->httpResponse()
            ->setData(view($carsView, compact('cars'))->render())
            ->setAdditional($additional)
            ->setMessage($message);
    }

    public function switchCurrency(Request $request, ?string $title = null)
    {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (! $title) {
            return $this->httpResponse();
        }

        $currency = Currency::query()->where('title', $title)->first();

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        $url = URL::previous();

        if (! $url || $url === URL::current()) {
            return $this
                ->httpResponse()
                ->setNextUrl(BaseHelper::getHomepageUrl());
        }

        if (Str::contains($url, ['min_price', 'max_price'])) {
            $url = preg_replace('/&min_price=[0-9]+/', '', $url);
            $url = preg_replace('/&max_price=[0-9]+/', '', $url);
        }

        return $this
            ->httpResponse()
            ->setNextUrl($url);
    }

  public function ajaxGetLocation(Request $request, BaseHttpResponse $response)
    {
        $keyword = BaseHelper::stringify($request->input('location') ?: $request->query('k'));
        $limit = (int) theme_option('limit_results_on_car_location_filter', 10) ?: 15;

        if (empty($keyword)) {
            return $response->setData([]);
        }

        $results = [];

        // 1. Fetch Geographic Locations (Cities/States/Countries)
        if (is_plugin_active('location')) {
            $cities = \Botble\Location\Models\City::query()
                ->wherePublished()
                ->where(function($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%')
                          ->orWhereHas('state', function($q) use ($keyword) {
                              $q->where('name', 'LIKE', '%' . $keyword . '%');
                          })
                          ->orWhereHas('country', function($q) use ($keyword) {
                              $q->where('name', 'LIKE', '%' . $keyword . '%');
                          });
                })
                ->whereExists(function ($query) {
                    $query->select('id')->from('cr_cars')->whereColumn('city_id', 'cities.id');
                })
                ->with(['state', 'country'])
                ->limit($limit)
                ->get();

            foreach ($cities as $city) {
                $fullName = $city->name;
                if ($city->state) {
                    $fullName .= ', ' . $city->state->name;
                }
                if ($city->country) {
                    $fullName .= ', ' . $city->country->name;
                }

                $results[] = [
                    'id' => $city->id, 
                    'name' => $fullName, 
                    'city_name' => $city->name,
                    'state_name' => $city->state ? $city->state->name : '',
                    'country_name' => $city->country ? $city->country->name : '',
                    'type' => 'city' // Tag as a geographic city
                ];
            }
        }

        // 2. Fetch Specific Areas, Hotels, and Airports from Car Addresses!
        $addresses = \Botble\CarRentals\Models\Car::query()
            ->whereNotNull('address')
            ->where('address', 'LIKE', '%' . $keyword . '%')
            ->select('address')
            ->distinct()
            ->limit($limit)
            ->pluck('address');

        foreach ($addresses as $address) {
            // Add the specific address to the dropdown list
            $results[] = [
                'id' => '', // No city ID needed for exact address matching
                'name' => $address, 
                'city_name' => $address,
                'state_name' => '',
                'country_name' => '',
                'type' => 'poi' // Tag as a Point of Interest (Hotel/Airport)
            ];
        }

        return $response->setData($results);
    }

    public function redirectToExternalBooking(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Car::class));

        abort_unless($slug, 404);

        $car = $slug->reference;

        abort_unless($car, 404);
        abort_unless(get_car_rentals_setting('enable_off_site_booking', true), 404);
        abort_unless($car->hasExternalBookingUrl(), 404);

        return redirect()->away($car->external_booking_url);
    }

    public function getMake(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(CarMake::class));

        abort_unless($slug, 404);

        $carMake = $slug->reference;

        abort_if($carMake->status->getValue() !== BaseStatusEnum::PUBLISHED, 404);

        SeoHelper::setTitle(__($carMake->name));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Cars'), route('public.cars'))
            ->add($carMake->name, $carMake->url);

        $query = Car::query()
            ->active()
            ->where('make_id', $carMake->getKey());

        $cars = $query->oldest('order')->latest()->paginate(CarRentalsHelper::getCarsPerPage());

        return Theme::scope('car-rentals.car-make', compact('cars', 'carMake'), 'plugins/car-rentals::themes.car-make')->render();
    }

    public function getTag(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(CarTag::class));

        abort_unless($slug, 404);

        $carTag = $slug->reference;

        abort_if($carTag->status->getValue() !== BaseStatusEnum::PUBLISHED, 404);

        SeoHelper::setTitle(__($carTag->name));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Cars'), route('public.cars'))
            ->add($carTag->name, $carTag->url);

        $query = Car::query()
            ->active()
            ->whereHas('tags', function ($query) use ($carTag): void {
                $query->where('cr_tags.id', $carTag->getKey());
            });

        $cars = $query->oldest('order')->latest()->paginate(CarRentalsHelper::getCarsPerPage());

        return Theme::scope('car-rentals.car-tag', compact('cars', 'carTag'), 'plugins/car-rentals::themes.car-tag')->render();
    }

    public function ajaxGetCarsMake(Request $request)
    {
        $requestQuery = CarListHelper::getCarFilters($request->input());

        $with = [
            'slugable',
            'transmission',
            'fuel',
            'city',
            'state',
            'country',
            'make',
        ];

        $sortBy = $requestQuery['sort_by'] ?? 'recently_added';

        $cars = app(CarInterface::class)->getCars(
            $requestQuery,
            [
                'with' => $with,
                'order_by' => $sortBy,
                'paginate' => [
                    'per_page' => $requestQuery['per_page'] ?? Arr::first(CarListHelper::getPerPageParams()),
                    'current_paged' => $requestQuery['page'] ?? 1,
                ],
            ],
        );

        $additional['total'] = $cars->total();

        if ($additional['total']) {
            $message = __(':total items found', [
                'total' => $cars->total(),
            ]);
        } else {
            $message = __('No results found');
        }

        $additional['message'] = $message;

        $carsView = Theme::getThemeNamespace('views.car-rentals.car-make');

        if (! view()->exists($carsView)) {
            $carsView = 'plugins/car-rentals::themes.includes.car-items';
        }

        $carMakeIds = $request->input('car_makes', []);
        $carMakeId = is_array($carMakeIds) ? (int) Arr::first($carMakeIds) : (int) $carMakeIds;
        $carMake = CarMake::query()->findOrFail($carMakeId);

        return $this
            ->httpResponse()
            ->setData(view($carsView, compact('cars', 'carMake'))->render())
            ->setAdditional($additional)
            ->setMessage($message);
    }

    /**
     * Subtotal, tax, fees, deposits, quote snapshot, and session price lock for checkout sidebar.
     *
     * @return array{
     *     serviceIds: array,
     *     services: \Illuminate\Support\Collection,
     *     insurances: \Illuminate\Support\Collection,
     *     rentalCarAmount: float,
     *     amount: float,
     *     taxAmount: float,
     *     taxTitle: string,
     *     discountAmount: float,
     *     couponCode: string|null,
     *     feeName: string,
     *     feeValue: float,
     *     feeAmount: float,
     *     depositType: string,
     *     depositRate: float,
     *     depositBaseAmount: float,
     *     depositAmount: float,
     *     depositRisk: array,
     *     totalAmount: float,
     *     finalPayableAmount: float,
     *     priceLock: array,
     *     priceLockExpiresAt: string|null,
     *     priceLockExpiredMessage: string,
     *     lockWasRefreshed: bool
     * }
     */
    protected function prepareCheckoutBookingPricing(array $sessionData, Car $car, Carbon $startDate, Carbon $endDate): array
    {
        $serviceIds = Arr::get($sessionData, 'service_ids', []);
        $couponCode = Arr::get($sessionData, 'coupon_code');
        
        // --- FIX: Extract the single Guest Plan ID from the session ---
        $guestProtectionPlanId = Arr::get($sessionData, 'guest_protection_plan_id');
        $guestProtectionPlanId = $guestProtectionPlanId ? (int) $guestProtectionPlanId : null;

        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            $startDate,
            $endDate,
            $serviceIds,
            $guestProtectionPlanId, // Changed from insurance_ids array
            $couponCode,
            Auth::guard('customer')->user()
        );

        $services = $quoteData['services'];
        
        // --- FIX: Extract the new Guest Plan Data ---
        $guestProtectionPlan = $quoteData['guest_protection_plan'];
        $guestProtectionFee = (float) $quoteData['guest_protection_fee'];
        
        $rentalCarAmount = (float) $quoteData['rental_amount'];
        $serviceAmount = (float) $quoteData['service_amount'];
        $amount = (float) $quoteData['subtotal'];
        $taxAmount = (float) $quoteData['tax_amount'];
        $taxTitle = (string) $quoteData['tax_title'];
        $discountAmount = (float) $quoteData['coupon_amount'];
        $feeName = (string) $quoteData['fee_name'];
        $feeValue = (float) $quoteData['fee_value'];
        $feeAmount = (float) $quoteData['fee_amount'];
        $depositType = (string) $quoteData['deposit_type'];
        $depositRate = (float) $quoteData['deposit_rate'];
        $depositFixedAmount = (float) $quoteData['deposit_fixed_amount'];
        $baseDepositAmount = (float) $quoteData['deposit_base_amount'];
        $depositRisk = $quoteData['deposit_risk'];
        $depositAmount = (float) $quoteData['deposit_amount'];
        $totalAmount = (float) $quoteData['total_amount'];
        $finalPayableAmount = (float) $quoteData['final_payable_amount'];

        BookingHelper::saveCheckoutData([
            'coupon_amount' => $discountAmount,
        ]);

        $priceLockService = app(PriceLockService::class);
        $priceLockExpiredMessage = $priceLockService->getExpiredMessage();

        $quote = [
            'rental_amount' => $rentalCarAmount,
            'service_amount' => $serviceAmount,
            'subtotal' => $amount,
            'tax_amount' => $taxAmount,
            'coupon_code' => $couponCode,
            'coupon_amount' => $discountAmount,
            'fee_name' => $feeName,
            'fee_value' => $feeValue,
            'fee_amount' => $feeAmount,
            'deposit_amount' => $depositAmount,
            'deposit_base_amount' => $baseDepositAmount,
            'deposit_type' => $depositType,
            'deposit_rate' => $depositRate,
            'deposit_fixed_amount' => $depositFixedAmount,
            'deposit_risk_multiplier' => (float) $depositRisk['multiplier'],
            'deposit_risk_level' => $depositRisk['risk_level'],
            'deposit_risk_reasons' => $depositRisk['reasons'],
            'total_amount' => $finalPayableAmount,
            'currency_id' => $car->currency_id,
            'tax_title' => $taxTitle,
            'services' => $services->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'price_type' => $service->price_type?->getValue(),
            ])->values()->all(),
        ];

        $freshSession = BookingHelper::getCheckoutData();
        $priceLock = Arr::get($freshSession, 'price_lock');
        $lockWasRefreshed = false;

        if (! $priceLock || $priceLockService->isExpired($priceLock) || ! $priceLockService->matchesSnapshot($priceLock, $quote)) {
            $priceLock = $priceLockService->createLock($quote);
            BookingHelper::saveCheckoutData([
                'coupon_amount' => $discountAmount,
                'price_lock' => $priceLock,
            ]);
            $lockWasRefreshed = true;
        }

        return [
            'serviceIds' => $serviceIds,
            'services' => $services,
            'rentalCarAmount' => $rentalCarAmount,
            'amount' => $amount,
            'taxAmount' => $taxAmount,
            'taxTitle' => $taxTitle,
            'discountAmount' => $discountAmount,
            'couponCode' => $couponCode,
            'feeName' => $feeName,
            'feeValue' => $feeValue,
            'feeAmount' => $feeAmount,
            'depositType' => $depositType,
            'depositRate' => $depositRate,
            'depositBaseAmount' => $baseDepositAmount,
            'depositAmount' => $depositAmount,
            'depositRisk' => $depositRisk,
            'totalAmount' => $totalAmount,
            'finalPayableAmount' => $finalPayableAmount,
            'priceLock' => $priceLock,
            'priceLockExpiresAt' => Arr::get($priceLock, 'expires_at'),
            'priceLockExpiredMessage' => $priceLockExpiredMessage,
            'lockWasRefreshed' => $lockWasRefreshed,
            
            // --- FIX: Return the new variables ---
            'guest_protection_plan' => $guestProtectionPlan,
            'guest_protection_fee' => $guestProtectionFee,
        ];
    }

    public function ajaxGetCombinedLocations(Request $request, StateRepository $stateRepository, CityRepository $cityRepository)
    {
        $keyword = BaseHelper::stringify($request->query('k'));
        $limit = (int) theme_option('limit_results_on_car_location_filter', 10) ?: 1000;

        $locations = collect();
        if (is_plugin_active('location')) {
            if ($request->input('type', 'state') === 'state') {
                $locations = $stateRepository->filters($keyword, $limit);
            } else {
                $locations = $cityRepository->filters($keyword, $limit);
                $locations->loadMissing('state');
            }
        }

        $bookingLocations = collect();
        if (CarRentalsHelper::isEnabledFilterCarsBy('addresses')) {
            $bookingLocations = City::query()
                ->wherePublished()
                ->withCount(['cars' => function ($query): void {
                    $query->wherePublished();
                }])
                ->when($keyword, function ($query) use ($keyword): void {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                })
                ->latest('cars_count')
                ->latest()
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'error' => false,
            'data' => [$locations],
            'booking_locations' => $bookingLocations,
            'total' => $locations->count() + $bookingLocations->count(),
        ]);
    }
}