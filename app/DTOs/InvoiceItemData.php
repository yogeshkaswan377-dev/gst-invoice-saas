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

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            quantity: (int) ($data['quantity'] ?? 1),
            unit_price: (float) ($data['unit_price'] ?? 0),
            description: $data['description'] ?? null,
            hsn_sac_code: $data['hsn_sac_code'] ?? null,
            unit: $data['unit'] ?? 'nos',
            original_unit_price: isset($data['original_unit_price']) ? (float) $data['original_unit_price'] : null,
            discount_type: $data['discount_type'] ?? null,
            discount_value: (float) ($data['discount_value'] ?? 0),
            discount_amount: (float) ($data['discount_amount'] ?? 0),
            gst_rate: (float) ($data['gst_rate'] ?? 18.00),
            taxable_amount: (float) ($data['taxable_amount'] ?? 0),
            productId: isset($data['productId']) ? (int) $data['productId'] : null,
            cgst_amount: (float) ($data['cgst_amount'] ?? 0),
            sgst_amount: (float) ($data['sgst_amount'] ?? 0),
            igst_amount: (float) ($data['igst_amount'] ?? 0),
            line_total: (float) ($data['line_total'] ?? 0),
            line_total_with_gst: (float) ($data['line_total_with_gst'] ?? 0),
            product_id: isset($data['product_id']) ? (int) $data['product_id'] : null,
            sort_order: (int) ($data['sort_order'] ?? 0),
        );
    }
}
