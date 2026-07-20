<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProformaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_mode' => 'required|in:select,manual',
            'client_id' => 'exclude_if:client_mode,manual|required|exists:clients,id',

            // Manual client fields
            'manual_client_name' => 'required_if:client_mode,manual|string|max:255',
            'manual_client_company' => 'nullable|string|max:255',
            'manual_client_gstin' => 'nullable|string|size:15',
            'manual_client_email' => 'nullable|email|max:255',
            'manual_client_phone' => 'nullable|string|max:20',
            'manual_client_address' => 'nullable|string|max:255',
            'manual_client_state_code' => 'required_if:client_mode,manual|string|size:2',
            'manual_client_state_name' => 'nullable|string|max:100',
            'manual_client_pincode' => 'nullable|string|max:10',

            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'reference_number' => 'nullable|string|max:50',

            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|in:0,5,12,18,28',
            'items.*.discount_type' => 'nullable|in:percentage,fixed',
            'items.*.discount_value' => 'nullable|numeric|min:0',

            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_charges' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'terms_and_conditions' => 'nullable|string|max:5000',
            'status' => 'nullable|in:draft,sent',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one item to the invoice.',
            'items.min' => 'Please add at least one item to the invoice.',
            'items.*.name.required' => 'Item name is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.gst_rate.in' => 'Invalid GST rate selected.',
        ];
    }
}
