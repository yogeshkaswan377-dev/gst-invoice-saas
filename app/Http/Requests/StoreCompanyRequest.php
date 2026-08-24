<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gstin' => 'nullable|string|size:15|unique:companies,gstin',
            'pan' => 'nullable|string|size:10|unique:companies,pan',
            'cin' => 'nullable|string|max:21',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'state_code' => 'nullable|string|size:2',
            'pincode' => 'nullable|string|size:6',
            'country' => 'nullable|string|default:India',
        ];
    }
    
    public function messages(): array
    {
        return [
            'gstin.size' => 'GSTIN must be exactly 15 characters',
            'gstin.unique' => 'This GSTIN is already registered with another company.',
            'pan.size' => 'PAN must be exactly 10 characters',
            'pincode.size' => 'Pincode must be exactly 6 digits',
        ];
    }
}