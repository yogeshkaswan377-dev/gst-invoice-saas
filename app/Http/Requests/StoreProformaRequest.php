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

            // Only required when mode = select
            'client_id' => 'required_if:client_mode,select|exists:clients,id',

            // Only required when mode = manual – use 'nullable' to allow absence in select mode
            'manual_client_name' => 'nullable|required_if:client_mode,manual|string|max:255',
            'manual_client_company' => 'nullable|string|max:255',
            'manual_client_gstin' => 'nullable|string|max:15',
            'manual_client_email' => 'nullable|email|max:255',
            'manual_client_phone' => 'nullable|string|max:20',
            'manual_client_address' => 'nullable|string|max:500',
            'manual_client_state_code' => 'nullable|required_if:client_mode,manual|string|size:2',
            'manual_client_state_name' => 'nullable|required_if:client_mode,manual|string|max:255',
            'manual_client_pincode' => 'nullable|string|max:10',

            // Items
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|in:0,5,12,18,28',

            // Invoice header
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'reference_number' => 'nullable|string|max:100',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_charges' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'reverse_charge' => 'boolean',
            'notes' => 'nullable|string|max:2000',
            'terms_and_conditions' => 'nullable|string|max:2000',
            'payment_terms' => 'nullable|string|max:100',
            'show_hsn_sac' => 'boolean',
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
