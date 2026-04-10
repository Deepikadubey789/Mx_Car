<?php

namespace Botble\CarRentals\Forms\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\DateField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\CarRentals\Http\Requests\Fronts\BookingRequest;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Service;
use Botble\CarRentals\Models\Insurance; // NEW IMPORT
use Botble\CarRentals\Services\PricingQuoteService;
use Botble\Theme\Facades\Theme;
use Botble\Theme\FormFront;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingForm extends FormFront
{
    public function setup(): void
    {
        Theme::asset()->add('booking-css', 'vendor/core/plugins/car-rentals/css/front-booking-form.css', version: get_cms_version());
        Theme::asset()->container('footer')->add('booking-js', 'vendor/core/plugins/car-rentals/js/front-booking-form.js', version: get_cms_version());

        $carId = $this->model['car_id'] ?? null;

        if (! $carId) {
            return;
        }

        $car = Car::query()->whereKey($carId)->first();

        if (! $car) {
            return;
        }

        $dateFormat = CarRentalsHelper::getDateFormat();

        $defaultStartDate = Carbon::now()->format($dateFormat);
        $defaultEndDate = Carbon::now()->addDay()->format($dateFormat);

        $startDate = BaseHelper::stringify(request()->query('rental_start_date', $defaultStartDate));
        $endDate = BaseHelper::stringify(request()->query('rental_end_date', $defaultEndDate));

        $startDateParsed = rescue(fn () => Carbon::createFromFormat($dateFormat, $startDate), null, false);
        $endDateParsed = rescue(fn () => Carbon::createFromFormat($dateFormat, $endDate), null, false);

        if (! $startDateParsed) {
            $startDate = $defaultStartDate;
        }

        if (! $endDateParsed) {
            $endDate = $defaultEndDate;
        }

        $startTime = BaseHelper::stringify(request()->query('rental_start_time', '09:00'));
        $endTime = BaseHelper::stringify(request()->query('rental_end_time', '09:00'));

        $quoteData = app(PricingQuoteService::class)->buildQuote(
            $car,
            Carbon::createFromFormat($dateFormat, $startDate),
            Carbon::createFromFormat($dateFormat, $endDate),
            [],
            [],
            null,
            Auth::guard('customer')->user()
        );

        // FETCH SERVICES
        $services = Service::query()->select(['id', 'name', 'price', 'currency_id'])->wherePublished()->get();
        $serviceOptions = [];
        foreach ($services as $service) {
            $serviceOptions[$service->id] = $service->name . ' - ' . $service->price_text;
        }

        // FETCH INSURANCES FOR THE CAR'S VENDOR
        $insurances = Insurance::query()
            ->where('vendor_id', $car->author_id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();
            
        $insuranceOptions = [];
        foreach ($insurances as $insurance) {
            $insuranceOptions[$insurance->id] = $insurance->name . ' - ' . format_price($insurance->price);
        }

        // Generate time options (every 30 minutes)
        $timeOptions = [];
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $timeOptions[$time] = $time;
            }
        }

        // Start building the form
        $this
            ->contentOnly()
            ->setUrl(route('public.booking'))
            ->model(Booking::class)
            ->setValidatorClass(BookingRequest::class)
            ->setFormOption('class', 'booking-form')
            ->setFormOption('data-estimate-url', route('public.ajax.booking.estimate'))
            ->add(
                'car_id',
                'hidden',
                TextFieldOption::make()
                    ->value($carId)
            )
            ->add(
                'rental_start_date',
                DateField::class,
                DatePickerFieldOption::make()
                    ->label(__('Start Date'))
                    ->value($startDate)
                    ->required()
            )
            ->add(
                'rental_start_time',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Start Time'))
                    ->choices($timeOptions)
                    ->selected($startTime)
                    ->required()
            )
            ->add(
                'rental_end_date',
                DateField::class,
                DatePickerFieldOption::make()
                    ->label(__('End Date'))
                    ->value($endDate)
                    ->required()
            )
            ->add(
                'rental_end_time',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('End Time'))
                    ->choices($timeOptions)
                    ->selected($endTime)
                    ->required()
            )
            ->add(
                'service_ids[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(__('Additional Services'))
                    ->choices($serviceOptions)
                    ->colspan(2)
            );

        // ONLY ADD THE INSURANCE FIELD IF THE VENDOR HAS INSURANCES CONFIGURED
        if (!empty($insuranceOptions)) {
            $this->add(
                'insurance_ids[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(__('Insurance Coverage'))
                    ->choices($insuranceOptions)
                    ->colspan(2)
            );
        }

        // Finish the form
        $this
            ->add('border_wrapper_after', HtmlField::class, HtmlFieldOption::make()->content('<div class="border-wrapper-after"></div>')->colspan(2))
            ->add(
                'total_estimate',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->view('plugins/car-rentals::cars.partials.booking-form-estimate', [
                        'total' => (float) $quoteData['final_payable_amount'],
                        'subtotal' => (float) $quoteData['subtotal'],
                        'tax' => (float) $quoteData['tax_amount'],
                        'taxInfo' => (string) $quoteData['tax_title'],
                        'discount' => (float) $quoteData['coupon_amount'],
                        'depositAmount' => (float) $quoteData['deposit_amount'],
                        'feeAmount' => (float) $quoteData['fee_amount'],
                        'rentalDays' => (int) $quoteData['rental_days'],
                        'baseRentalAmount' => (float) $quoteData['base_rental_amount'],
                        'rentalAmount' => (float) $quoteData['rental_amount'],
                        'policyDiscountAmount' => (float) $quoteData['policy_discount_amount'],
                        'policyDiscountSource' => (string) ($quoteData['policy_discount_source'] ?? ''),
                        'serviceAmount' => (float) $quoteData['service_amount'],
                        'insuranceAmount' => (float) $quoteData['insurance_amount'],
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
                        'currencyId' => $car->currency_id,
                    ])
                    ->colspan(2)
            )
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->cssClass('btn btn-primary')
                    ->label(__('Book Now'))
            );
    }
}