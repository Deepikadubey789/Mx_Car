<?php

namespace Botble\CarRentals\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class GetRecommendationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated as a customer/vendor
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                'in:pending,applied,dismissed,all',
            ],
            'car_id' => [
                'sometimes',
                'integer',
                'exists:cr_cars,id',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: pending, applied, dismissed, or all',
            'status.string' => 'Status must be a string',
            'car_id.integer' => 'Car ID must be an integer',
            'car_id.exists' => 'The selected car does not exist',
            'per_page.integer' => 'Per page must be an integer',
            'per_page.min' => 'Per page must be at least 1',
            'per_page.max' => 'Per page cannot exceed 100',
            'page.integer' => 'Page must be an integer',
            'page.min' => 'Page must be at least 1',
        ];
    }

    /**
     * Get the validated data from the request with defaults.
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // Set default values if not provided
        if (is_array($validated)) {
            $validated['status'] = $this->input('status', 'all');
            $validated['per_page'] = $this->integer('per_page', 15);
            $validated['page'] = $this->integer('page', 1);
        }

        return $validated;
    }
}
