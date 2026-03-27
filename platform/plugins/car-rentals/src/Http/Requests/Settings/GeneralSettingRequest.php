<?php

namespace Botble\CarRentals\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\CarRentals\Enums\CommissionFeeTypeEnum;
use Botble\CarRentals\Enums\DistanceUnitEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class GeneralSettingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'enabled_multi_vendor' => $onOffRule = new OnOffRule(),
            'enable_post_approval' => $onOffRule,
            'enabled_car_rental' => $onOffRule,
            'enabled_car_sale' => $onOffRule,
            'booking_number_prefix' => ['nullable', 'string'],
            'booking_number_suffix' => ['nullable', 'string'],
            'rental_commission_fee' => ['required', 'numeric', 'min:0', 'max:100'],
            'commission_fee_type' => ['required', 'string', Rule::in(CommissionFeeTypeEnum::values())],
            'enable_commission_fee_for_each_category' => ['nullable', 'boolean'],
            'distance_unit' => ['required', 'string', Rule::in(DistanceUnitEnum::values())],
            'hide_owner_info' => $onOffRule,
            'hide_owner_phone' => $onOffRule,
            'hide_owner_email' => $onOffRule,
            'hide_owner_whatsapp' => $onOffRule,
            'enable_off_site_booking' => $onOffRule,
            'enable_message_form' => $onOffRule,
            'disable_one_way_rental' => $onOffRule,
        ];

        if ($this->input('enable_commission_fee_for_each_category') == 1) {
            $commissionByCategory = $this->input('commission_by_category');
            if (is_array($commissionByCategory)) {
                foreach ($commissionByCategory as $key => $item) {
                    $rules['commission_by_category.' . $key . '.commission_fee'] = ['required', 'numeric', 'min:0', 'max:100'];
                    $rules['commission_by_category.' . $key . '.categories'] = ['required'];
                }
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];

        if ($this->input('enable_commission_fee_for_each_category') == 1) {
            // validate request setting category commission
            $commissionByCategory = $this->input('commission_by_category');
            if (is_array($commissionByCategory)) {
                foreach ($commissionByCategory as $key => $item) {
                    $commissionFeeName = sprintf('%s.%s.commission_fee', 'commission_by_category', $key);
                    $categoryName = sprintf('%s.%s.categories', 'commission_by_category', $key);
                    $attributes[$commissionFeeName] = trans('plugins/car-rentals::settings.commission.commission_fee_each_category_fee_name', ['key' => $key]);
                    $attributes[$categoryName] = trans('plugins/car-rentals::settings.commission.commission_fee_each_category_name', ['key' => $key]);
                }
            }
        }

        return $attributes;
    }
}
