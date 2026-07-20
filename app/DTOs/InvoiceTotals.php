<?php

namespace App\DTOs;

class InvoiceTotals
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discountAmount,
        public readonly float $taxableAmount,
        public readonly float $totalGst,
        public readonly float $cgstAmount,
        public readonly float $sgstAmount,
        public readonly float $igstAmount,
        public readonly float $grandTotal,
        public readonly ?TaxBreakdown $taxBreakdown = null,
        public readonly float $shippingCharges = 0,
        public readonly float $commission = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => round($this->subtotal, 2),
            'discount_amount' => round($this->discountAmount, 2),
            'taxable_amount' => round($this->taxableAmount, 2),
            'total_gst' => round($this->totalGst, 2),
            'grand_total' => round($this->grandTotal, 2),
            'tax_breakdown' => $this->taxBreakdown?->toArray(),
            'shipping_charges' => round($this->shippingCharges, 2),
            'commission' => round($this->commission, 2),
        ];
    }
}
