<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'email'     => ['nullable', 'email', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'address'   => ['nullable', 'string', 'max:500'],
            'city'      => ['nullable', 'string', 'max:100'],
            'state'     => ['nullable', 'string', 'max:100'],
            'pincode'   => ['nullable', 'string', 'max:10'],
            'logo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ];
    }
}
