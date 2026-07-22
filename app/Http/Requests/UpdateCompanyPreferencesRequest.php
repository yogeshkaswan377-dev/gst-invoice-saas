<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_prefix'  => ['required', 'string', 'max:10'],
            'quote_prefix'    => ['required', 'string', 'max:10'],   // changed
            'payment_terms' => ['required', 'in:0,7,15,30,45'],
        ];
    }
}
