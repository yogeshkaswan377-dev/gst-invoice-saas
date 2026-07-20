<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'item_no' => $this->item_no,
            'name' => $this->name,
            'description' => $this->description,
            'hsn_sac_code' => $this->hsn_sac_code,
            'unit_price' => $this->unit_price,
            'gst_rate' => $this->gst_rate,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
            'stock' => $this->stock,
            'stock_unit' => $this->stock_unit,
            'stock_deduction_type' => $this->stock_deduction_type,
            'consumption_per_piece' => $this->consumption_per_piece,
            'minimum_stock' => $this->minimum_stock,
            'selling_price' => $this->selling_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
