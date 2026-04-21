<?php

namespace Botble\CarRentals\Forms\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption; // NEW IMPORT
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\DateField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\RadioField; // NEW IMPORT
use Botble\Base\Forms\Fields\SelectField;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\CarRentals\Http\Requests\Fronts\BookingRequest;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\Service;
use Botble\CarRentals\Models\GuestProtectionPlan; // NEW IMPORT
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
            [], // Empty array for serviceIds
            null, // FIX: Pass null instead of [] for guestProtectionPlanId
            null, // couponCode
            Auth::guard('customer')->user()
        );

        $bookingBlockedByCategory = ($quoteData['eligibility_state'] ?? '') === 'blocked'
            && in_array('category_requires_driver_verified_kyc', $quoteData['eligibility_reasons'] ?? [], true);

        // FETCH SERVICES
        $services = Service::query()->select(['id', 'name', 'price', 'currency_id'])->wherePublished()->get();
        $serviceOptions = [];
        foreach ($services as $service) {
            $serviceOptions[$service->id] = $service->name . ' - ' . $service->price_text;
        }

// --- NEW: FETCH GUEST PROTECTION PLANS ---
        $guestProtectionPlans = GuestProtectionPlan::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->get();
            
        $guestProtectionOptions = [];
        $guestProtectionOptions[''] = __('No Protection ($0.00/day)'); 
        
        foreach ($guestProtectionPlans as $plan) {
            $label = $plan->name . ' - ' . format_price($plan->daily_fee) . '/' . __('day') . ' ' . __('(Deductible: :amount)', ['amount' => format_price($plan->deductible_amount)]);
            $guestProtectionOptions[$plan->id] = $label;
        }

        // Generate time options (every 30 minutes)
        $timeOptions = [];
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $timeOptions[$time] = $time;
            }
        }

       // --- NEW: FETCH DELIVERY LOCATIONS ---
        $deliveryOptions = ['' => __('No Delivery (Pickup at Host Location)')];
        $car->loadMissing('deliveryLocations');
        
        $customAddressLocationId = null; // NEW: Track the custom location ID
        
        if ($car->is_delivery_enabled && $car->deliveryLocations->count() > 0) {
            foreach ($car->deliveryLocations as $location) {
                $deliveryOptions[$location->id] = $location->name . ' (+' . format_price($location->fee_amount) . ')';
                
                // FIX: Look for the word "Custom" in the zone's name instead of a database column
                if (stripos($location->name, 'custom') !== false || stripos($location->name, 'address') !== false) {
                    $customAddressLocationId = $location->id;
                }
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

        // --- NEW: ADD THE DELIVERY LOCATION FIELD AS RADIO BUTTONS ---
        if ($car->is_delivery_enabled && count($deliveryOptions) > 1) {
            
            $thresholdHelpText = '';
            if ($car->free_delivery_days_threshold) {
                 $thresholdHelpText = '<span class="text-success mt-2 d-inline-block"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg> ' . __('Free delivery on trips of :days days or more!', ['days' => $car->free_delivery_days_threshold]) . '</span>';
            }

            $this->add(
                'delivery_location_id',
                RadioField::class, // CHANGED FROM SelectField
                RadioFieldOption::make() // CHANGED FROM SelectFieldOption
                    ->label(__('Delivery Location'))
                    ->choices($deliveryOptions)
                    ->selected('') // Default to "No delivery"
                    ->helperText($thresholdHelpText)
                    ->colspan(2)
            );
        }
           // --- NEW: ADD THE CUSTOM ADDRESS TEXT BOX (Hidden by default) ---
        if ($customAddressLocationId) {
            $customAddressHtml = '
            <div id="custom-address-wrapper" style="display: none;" class="mt-2 mb-4 p-3 bg-light rounded border">
                <label class="form-label fw-bold">' . __('Enter Exact Delivery Address') . ' <span class="text-danger">*</span></label>
                <input type="text" name="custom_delivery_address" id="custom_delivery_address_input" class="form-control" placeholder="' . __('e.g., 123 Main St, Rome, Italy') . '">
                <small class="text-muted d-block mt-1">' . __('Car will be delivered if your address is within the host\'s maximum allowed radius.') . '</small>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const wrapper = document.getElementById("custom-address-wrapper");
                    const radios = document.querySelectorAll("input[name=\'delivery_location_id\']");
                    const customId = "' . $customAddressLocationId . '";
                    
                    // FIX: Select the input field
                    const addressInput = document.getElementById("custom_delivery_address_input");
                    
                    function toggleAddressBox() {
                        const selected = document.querySelector("input[name=\'delivery_location_id\']:checked");
                        if (selected && selected.value === customId) {
                            wrapper.style.display = "block";
                        } else {
                            wrapper.style.display = "none";
                        }
                    }

                    radios.forEach(r => r.addEventListener("change", toggleAddressBox));
                    toggleAddressBox(); // Check on initial load
                    
                    // FIX: Force the form to recalculate when the user clicks out of the text box
                    if (addressInput) {
                        addressInput.addEventListener("change", function() {
                            if (typeof window.jQuery !== "undefined") {
                                window.jQuery(this).closest("form").trigger("change");
                            }
                        });
                    }
                });
            </script>
            ';

            $this->add(
                'custom_delivery_address_html',
                HtmlField::class,
                HtmlFieldOption::make()->content($customAddressHtml)->colspan(2)
            );
        }
        // ----------------------------------------------------------------

        // --- ADD THE GUEST PROTECTION PLAN FIELD AS A RADIO BUTTON ---
        if (!empty($guestProtectionOptions)) {
            $this->add(
                'guest_protection_plan_id',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(__('Protection Plan'))
                    ->choices($guestProtectionOptions)
                    ->colspan(2)
            );
        }

        // Finish the form
        $categoryBookingNotice = '';
        if ($bookingBlockedByCategory) {
            if (Auth::guard('customer')->check()) {
                $categoryBookingNotice = '<div class="alert alert-warning mb-3" role="alert">'
                    . e(__('This vehicle category requires full driver verification before you can book.'))
                    . ' <a href="' . e(route('customer.kyc')) . '" class="alert-link">' . e(__('Complete verification')) . '</a>'
                    . '</div>';
            } else {
                $categoryBookingNotice = '<div class="alert alert-warning mb-3" role="alert">'
                    . e(__('This vehicle category requires a verified driver account to book.'))
                    . ' <a href="' . e(route('customer.login')) . '" class="alert-link">' . e(__('Log in')) . '</a>'
                    . '</div>';
            }
        }

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
                        
                        // --- FIX: Pass the new Guest Protection Fee instead of old insurance array ---
                        'guestProtectionFee' => (float) $quoteData['guest_protection_fee'],
                        // --- NEW: Add the default delivery fee for initial page load ---
                        'deliveryFee' => 0,
                        // ---------------------------------------------------------------
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
            );

        if ($categoryBookingNotice !== '') {
            $this->add(
                'restricted_category_notice',
                HtmlField::class,
                HtmlFieldOption::make()->content($categoryBookingNotice)->colspan(2)
            );
        }

        $this
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->cssClass('btn btn-primary')
                    ->label(__('Book Now'))
                    ->disabled($bookingBlockedByCategory)
            );
    }
}