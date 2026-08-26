<?php

namespace App\DTOs;

class InvoiceData
{
    public function __construct(
        public readonly int $company_id,
        public readonly int $client_id,
        public readonly int $created_by,
        public readonly string $invoice_type,
        public readonly string $gst_mode,
        public readonly float $gst_rate = 18.00,
        public readonly string $invoice_date,
        public readonly string $due_date,
        public readonly ?string $reference_number = null,
        public readonly ?string $place_of_supply = null,
        public readonly ?string $place_of_supply_state_code = null,
        public readonly bool $reverse_charge = false,
        public readonly float $subtotal = 0,
        public readonly ?string $discount_type = null,
        public readonly float $discount_amount = 0,
        public readonly float $taxable_amount = 0,
        public readonly float $cgst_amount = 0,
        public readonly float $sgst_amount = 0,
        public readonly float $igst_amount = 0,
        public readonly float $total_gst_amount = 0,
        public readonly float $shipping_charges = 0,
        public readonly float $commission = 0,
        public readonly float $grand_total = 0,
        public readonly ?string $notes = null,
        public readonly ?string $terms_and_conditions = null,
        public readonly string $payment_terms = 'Net 15',
        public readonly ?array $logistics_details = null,
        public readonly ?string $estimated_delivery_date = null,
        public readonly bool $show_hsn_sac = true,
        public readonly array $items = [],
        public readonly ?int $updated_by = null,
        public readonly ?string $status = null,
    ) {}

    public function toArray(): array
    {
        return [
            'company_id' => $this->company_id,
            'client_id' => $this->client_id,
            'created_by' => $this->created_by,
            'invoice_type' => $this->invoice_type,
            'gst_mode' => $this->gst_mode,
            'gst_rate' => $this->gst_rate,
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'reference_number' => $this->reference_number,
            'place_of_supply' => $this->place_of_supply,
            'place_of_supply_state_code' => $this->place_of_supply_state_code,
            'reverse_charge' => $this->reverse_charge,
            'subtotal' => $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_amount' => $this->discount_amount,
            'taxable_amount' => $this->taxable_amount,
            'cgst_amount' => $this->cgst_amount,
            'sgst_amount' => $this->sgst_amount,
            'igst_amount' => $this->igst_amount,
            'total_gst_amount' => $this->total_gst_amount,
            'shipping_charges' => $this->shipping_charges,
            'commission' => $this->commission,
            'grand_total' => $this->grand_total,
            'notes' => $this->notes,
            'terms_and_conditions' => $this->terms_and_conditions,
            'payment_terms' => $this->payment_terms,
            'logistics_details' => $this->logistics_details,
            'estimated_delivery_date' => $this->estimated_delivery_date,
            'show_hsn_sac' => $this->show_hsn_sac,
            'items' => $this->items,
            'updated_by' => $this->updated_by,
            'status' => $this->status,
        ];
    }
}
