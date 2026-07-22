<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_details'               => ['required', 'array'],
            'bank_details.*.bank_name'    => ['required', 'string', 'max:255'],
            'bank_details.*.account_number' => ['required', 'string', 'max:30'],
            'bank_details.*.account_holder_name' => ['required', 'string', 'max:255'],
            'bank_details.*.ifsc_code'    => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        ];
    }
}
