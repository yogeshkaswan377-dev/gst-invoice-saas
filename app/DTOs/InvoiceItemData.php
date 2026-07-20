<?php

namespace App\DTOs;

class InvoiceItemData
{
    public function __construct(
        public readonly string $name,
        public readonly int $quantity,
        public readonly float $unit_price,
        public readonly ?string $description = null,
        public readonly ?string $hsn_sac_code = null,
        public readonly string $unit = 'nos',
        public readonly ?float $original_unit_price = null,
        public readonly ?string $discount_type = null,
        public readonly float $discount_value = 0,
        public readonly float $discount_amount = 0,
        public readonly float $gst_rate = 18.00,
        public readonly float $taxable_amount = 0,
        public readonly ?int $productId = null, 
        public readonly float $cgst_amount = 0,
        public readonly float $sgst_amount = 0,
        public readonly float $igst_amount = 0,
        public readonly float $line_total = 0,
        public readonly float $line_total_with_gst = 0,
        public readonly ?int $product_id = null,
        public readonly int $sort_order = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'description' => $this->description,
            'hsn_sac_code' => $this->hsn_sac_code,
            'unit' => $this->unit,
            'original_unit_price' => $this->original_unit_price,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount_amount' => $this->discount_amount,
            'gst_rate' => $this->gst_rate,
            'taxable_amount' => $this->taxable_amount,
            'cgst_amount' => $this->cgst_amount,
            'sgst_amount' => $this->sgst_amount,
            'igst_amount' => $this->igst_amount,
            'line_total' => $this->line_total,
            'line_total_with_gst' => $this->line_total_with_gst,
            'product_id' => $this->product_id,
            'sort_order' => $this->sort_order,
        ];
    }
}
