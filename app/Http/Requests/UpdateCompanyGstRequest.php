<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyGstRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = auth()->user()->current_company_id;

        return [
            'gstin' => [
                'required',
                'string',
                'size:15',
                Rule::unique('companies', 'gstin')->ignore($companyId),
            ],
            'pan' => ['nullable', 'string', 'size:10'],
            'default_gst_rate' => ['required', 'numeric', 'in:0,5,12,18,28'],
            'gst_mode' => ['required', 'in:exclusive,inclusive'],
        ];
    }
}
