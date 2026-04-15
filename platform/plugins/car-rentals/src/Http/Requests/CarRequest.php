<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\CarRentals\Enums\CarConditionEnum;
use Botble\CarRentals\Enums\CarForSaleStatusEnum;
use Botble\CarRentals\Enums\CarRentalTypeEnum;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Models\CarCategory;
use Botble\CarRentals\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CarRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'rental_rate' => $this->formatPriceValue($this->input('rental_rate')),
            'sale_price' => $this->formatPriceValue($this->input('sale_price')),
            'is_for_sale' => $this->input('car_purpose') === 'sale',
        ]);

        // Filter out empty trip discount rows to avoid validation errors
        $tripDiscounts = $this->input('trip_discounts');
        if (is_array($tripDiscounts) && count($tripDiscounts) > 0) {
            $filteredDiscounts = [];
            foreach ($tripDiscounts as $discount) {
                if (!is_array($discount)) {
                    continue;
                }
                // Keep row if it has ANY meaningful data
                $min_days = trim((string)($discount['min_days'] ?? ''));
                $max_days = trim((string)($discount['max_days'] ?? ''));
                $discount_type = trim((string)($discount['discount_type'] ?? ''));
                $discount_value = trim((string)($discount['discount_value'] ?? ''));
                
                if ($min_days || $max_days || $discount_type || $discount_value) {
                    $filteredDiscounts[] = $discount;
                }
            }
            // Set to null if no valid rows, otherwise re-index array
            $this->merge(['trip_discounts' => count($filteredDiscounts) > 0 ? array_values($filteredDiscounts) : null]);
        }
    }

    protected function formatPriceValue(string|float|null $number): float
    {
        if (! $number) {
            return 0;
        }

        $decimalSeparator = get_car_rentals_setting('decimal_separator', '.');

        if ($decimalSeparator == 'space') {
            $decimalSeparator = ' ';
        }

        $thousandSeparator = get_car_rentals_setting('thousands_separator', ',');

        if ($thousandSeparator == 'space') {
            $thousandSeparator = ' ';
        }

        $number = str_replace($thousandSeparator, '', $number);

        $number = str_replace($decimalSeparator, '.', $number);

        return (float) $number;
    }

    public function rules(): array
    {
        $isForSale = $this->input('car_purpose') === 'sale';
        $isForRent = $this->input('car_purpose') === 'rent';

        return [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['nullable', 'max:1000'],
            'content' => ['nullable', 'string', 'max:300000'],
            'location' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'make_id' => ['nullable', 'int'],
            'vehicle_type_id' => ['nullable', 'int'],
            'transmission_id' => ['nullable', 'int'],
            'fuel_type_id' => ['nullable', 'int'],
            'year' => ['nullable', 'int', 'min:1900', 'max:3000'],
            'mileage' => ['nullable', 'int', 'min:0', 'max:10000000'],
            'fuel_rate_per_liter' => ['nullable', 'numeric', 'min:0'],
            'late_fee_per_hour' => ['nullable', 'numeric', 'min:0'],
            'horsepower' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'number_of_seats' => ['nullable', 'int', 'min:0', 'max:10000'],
            'number_of_doors' => ['nullable', 'int', 'min:0', 'max:10000'],
            'car_purpose' => ['nullable', 'string', Rule::in(['rent', 'sale'])],
            'rental_rate' => [
                'numeric',
                'min:0',
                'max:1000000000',
                Rule::requiredIf(fn () => $isForRent),
            ],
            'rental_type' => [
                'string',
                Rule::in(CarRentalTypeEnum::values()),
                Rule::requiredIf(fn () => $isForRent),
            ],
            'tax_id' => [
                'nullable',
                'exists:cr_taxes,id',
            ],
            'license_plate' => ['nullable', 'string', 'max:1000'],
            'vin' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:1000'],
            'status' => ['required', 'string', Rule::in(CarStatusEnum::values())],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['sometimes', Rule::exists((new CarCategory())->getTable(), 'id')],
            'is_featured' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'colors' => ['nullable', 'string', 'max:1000'],
            'is_used' => new OnOffRule(),
            'is_for_sale' => ['sometimes', 'boolean'],
            'sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000000000',
                Rule::requiredIf(fn () => $isForSale),
            ],
            'condition' => [
                'nullable',
                'string',
                Rule::in(CarConditionEnum::values()),
                Rule::requiredIf(fn () => $isForSale),
            ],
            'ownership_history' => ['nullable', 'string', 'max:1000'],
            'insurance_info' => ['nullable', 'string', 'max:5000'],
            'warranty_information' => ['nullable', 'string', 'max:5000'],
            'sale_status' => [
                'nullable',
                'string',
                Rule::in(CarForSaleStatusEnum::values()),
                Rule::requiredIf(fn () => $isForSale),
            ],
            'external_booking_url' => [
                'nullable',
                'url',
                'max:2000',
            ],
            'currency_id' => [
                'nullable',
                'integer',
                'exists:cr_currencies,id',
            ],
            'weekly_discount_type' => ['nullable', 'string', Rule::in(['none', 'percentage', 'fixed'])],
            'weekly_discount_value' => ['nullable', 'numeric', 'min:0'],
            'monthly_discount_type' => ['nullable', 'string', Rule::in(['none', 'percentage', 'fixed'])],
            'monthly_discount_value' => ['nullable', 'numeric', 'min:0'],
            'included_distance_per_day' => ['nullable', 'integer', 'min:0'],
            'included_distance_per_trip' => ['nullable', 'integer', 'min:0'],
            'extra_distance_unit_price' => ['nullable', 'numeric', 'min:0'],
            'distance_unit' => ['nullable', 'string', Rule::in(['km', 'miles'])],
            'distance_overage_billing_mode' => ['nullable', 'string', Rule::in(['end_of_trip', 'prepaid_estimate', 'both'])],
            'allow_best_discount_only' => ['nullable', 'boolean'],
            'max_discount_cap_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'demand_recommendations_enabled' => ['nullable', 'boolean'],
            'demand_min_price' => ['nullable', 'numeric', 'min:0'],
            'demand_max_price' => ['nullable', 'numeric', 'min:0'],
            'demand_max_daily_change_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trip_discounts' => ['nullable', 'array'],
            'trip_discounts.*.id' => ['nullable', 'integer'],
            'trip_discounts.*.min_days' => ['nullable', 'integer', 'min:1'],
            'trip_discounts.*.max_days' => ['nullable', 'integer', 'min:1'],
            'trip_discounts.*.discount_type' => ['nullable', 'string', Rule::in(['percentage', 'fixed'])],
            'trip_discounts.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'trip_discounts.*.priority' => ['nullable', 'integer', 'min:0'],
            'trip_discounts.*.active' => ['nullable', 'boolean'],
            'trip_discounts.*.description' => ['nullable', 'string', 'max:255'],
            'author_id' => [
                'nullable',
                Rule::exists(Customer::class, 'id'),
            ],
        ];
    }
}
