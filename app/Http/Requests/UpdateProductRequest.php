<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;

    class UpdateProductRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            $companyId = auth()->user()->current_company_id;
            $productId = $this->route('product')->id;

            return [
                'item_no' => ['required', 'string', 'max:50', Rule::unique('products')->where(fn($q) => $q->where('company_id', $companyId))->ignore($productId)],
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'hsn_sac_code' => 'nullable|string|max:8',
                'unit_price' => 'required|numeric|min:0',
                'gst_rate' => 'required|numeric|in:0,5,12,18,28',
                'unit' => 'required|string|max:20',
                'stock_unit' => 'required|string|max:20',
                'stock_deduction_type' => 'required|in:Meter,Piece,Kg,Roll,Box,Custom',
                'consumption_per_piece' => 'nullable|numeric|min:0|required_if:stock_deduction_type,Piece',
                'minimum_stock' => 'nullable|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'is_active' => 'boolean',
            ];
        }
    }
