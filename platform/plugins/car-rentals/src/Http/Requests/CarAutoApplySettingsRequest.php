<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CarAutoApplySettingsRequest extends Request
{
    public function rules(): array
    {
        return [
            'demand_auto_apply_enabled' => ['sometimes', 'boolean'],
            'demand_auto_apply_min_confidence' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'demand_auto_apply_max_daily_change_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'demand_auto_apply_pause_hours' => ['nullable', 'integer', 'min:1', 'max:336'], // max 2 weeks
        ];
    }

    public function attributes(): array
    {
        return [
            'demand_auto_apply_enabled' => 'Enable auto-apply',
            'demand_auto_apply_min_confidence' => 'Minimum confidence threshold',
            'demand_auto_apply_max_daily_change_percent' => 'Max daily change percent',
            'demand_auto_apply_pause_hours' => 'Pause duration (hours)',
        ];
    }
}
