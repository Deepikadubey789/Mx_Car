<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\CarRentals\Enums\CustomerStatusEnum;
use Botble\CarRentals\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends Request
{
    public function rules(): array
    {
        $ignoreId = $this->route('customer.id') ?: $this->route('vendor.id');

        $rules = [
            'name' => ['required', 'string', 'max:120', 'min:2'],
            'email' => [
                'required',
                new EmailRule(),
                Rule::unique((new Customer())->getTable(), 'email')->ignore($ignoreId),
            ],
            'phone' => ['nullable', 'string', ...BaseHelper::getPhoneValidationRule(true)],
            'avatar' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'status' => [  Rule::requiredIf(fn () => $this->input('is_vendor') == 1),'string',Rule::in(CustomerStatusEnum::values()),],
            'whatsapp' => ['nullable', 'string', 'max:25'],
            'is_vendor' => ['sometimes', 'boolean'],
        ];

        if ($this->boolean('is_change_password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        return $rules;
    }
}
