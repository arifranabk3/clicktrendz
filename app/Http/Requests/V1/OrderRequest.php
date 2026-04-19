<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * ClickTrendz Mandatory 4-point check logic.
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:15',
            'shipping_address' => 'required|string|min:5', // Relaxed here to allow "Incomplete" logic in Observer
            'shipping_city' => 'required|string|max:100',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,card',
        ];
    }

    /**
     * Custom messages for ClickTrendz strict data integrity.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is mandatory for delivery.',
            'customer_phone.required' => 'Phone number is required for courier coordination.',
            'shipping_address.required' => 'Complete street address is mandatory.',
            'shipping_city.required' => 'Destination city must be specified.',
        ];
    }
}
