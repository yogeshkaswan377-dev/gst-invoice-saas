<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyGstRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gstin'           => ['nullable', 'string', 'size:15'],
            'pan'             => ['nullable', 'string', 'size:10'],
            'default_gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
            'gst_mode'        => ['required', 'in:exclusive,inclusive'],
        ];
    }
}
