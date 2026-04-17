<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Support\Http\Requests\Request;

class WhatsAppMessageTemplateRequest extends Request
{
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'template_content' => 'required|string|max:5000',
            'placeholders' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ];
    }
}
