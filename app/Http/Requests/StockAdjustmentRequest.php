<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adjustment_type' => 'required|in:add,deduct,set',
            'quantity' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ];
    }
}
