<?php

namespace App\Services\GST;

use App\DTOs\InvoiceTotals;
use App\DTOs\TaxBreakdown;
use Illuminate\Support\Collection;

class TaxBreakdownService
{
    public function __construct(
        private GSTCalculationService $gstService
    ) {}

    /**
     * Calculate complete invoice tax breakdown
     */
    public function calculateInvoiceTax(
        float $subtotal,
        float $discountPercentage,
        string $mode,
        string $sellerState,
        string $buyerState,
        float $gstRate = 18.0,
        float $shippingCharges = 0,
        float $commission = 0
    ): InvoiceTotals {
        $discountAmount = round($subtotal * ($discountPercentage / 100), 2);
        $taxableAmount = round($subtotal - $discountAmount, 2);

        // Calculate GST on taxable amount
        if ($mode === 'exclusive') {
            $taxBreakdown = $this->gstService->calculateExclusiveGST(
                $taxableAmount,
                $sellerState,
                $buyerState,
                $gstRate
            );
        } else {
            $taxBreakdown = $this->gstService->calculateInclusiveGST(
                $taxableAmount,
                $sellerState,
                $buyerState,
                $gstRate
            );
        }

        $grandTotal = round($taxBreakdown->finalPrice + $shippingCharges + $commission, 2);

        return new InvoiceTotals(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            taxableAmount: $taxableAmount,
            totalGst: $taxBreakdown->totalGstAmount,
            cgstAmount: $taxBreakdown->cgstAmount,
            sgstAmount: $taxBreakdown->sgstAmount,
            igstAmount: $taxBreakdown->igstAmount,
            grandTotal: $grandTotal,
            taxBreakdown: $taxBreakdown,
            shippingCharges: $shippingCharges,
            commission: $commission
        );
    }

    /**
     * Calculate tax breakdown for multiple items
     */
    public function calculateMultiItemTax(
        Collection $items,
        string $mode,
        string $sellerState,
        string $buyerState,
        float $defaultGstRate = 18.0
    ): InvoiceTotals {
        $subtotal = 0;
        $itemBreakdowns = [];

        foreach ($items as $item) {
            $itemGstRate = $item['gst_rate'] ?? $defaultGstRate;
            $itemTotal = $item['price'] * $item['quantity'];
            $itemDiscount = round($itemTotal * (($item['discount'] ?? 0) / 100), 2);
            $itemTaxable = $itemTotal - $itemDiscount;
            $subtotal += $itemTaxable;

            $itemTaxBreakdown = $this->gstService->calculateItemGST(
                itemPrice: $item['price'],
                quantity: $item['quantity'],
                discount: $item['discount'] ?? 0,
                mode: $mode,
                sellerState: $sellerState,
                buyerState: $buyerState,
                gstRate: $itemGstRate
            );

            $itemBreakdowns[] = $itemTaxBreakdown;
        }

        // Calculate total tax
        $totalGstAmount = array_sum(array_map(fn($b) => $b->totalGstAmount, $itemBreakdowns));
        $totalBasePrice = array_sum(array_map(fn($b) => $b->basePrice, $itemBreakdowns));

        $taxType = $this->gstService->determineTaxType($sellerState, $buyerState);

        $taxBreakdown = new TaxBreakdown(
            taxType: $taxType,
            taxableValue: $totalBasePrice,
            cgstAmount: array_sum(array_map(fn($b) => $b->cgstAmount, $itemBreakdowns)),
            sgstAmount: array_sum(array_map(fn($b) => $b->sgstAmount, $itemBreakdowns)),
            igstAmount: array_sum(array_map(fn($b) => $b->igstAmount, $itemBreakdowns)),
            totalGstAmount: $totalGstAmount,
            cgstRate: $itemBreakdowns[0]->cgstRate ?? 0,
            sgstRate: $itemBreakdowns[0]->sgstRate ?? 0,
            igstRate: $itemBreakdowns[0]->igstRate ?? 0,
            basePrice: $totalBasePrice,
            finalPrice: $totalBasePrice + $totalGstAmount,
            mode: $mode,
            itemBreakdown: $itemBreakdowns
        );

        return new InvoiceTotals(
            subtotal: $subtotal,
            discountAmount: 0,
            taxableAmount: $totalBasePrice,
            totalGst: $totalGstAmount,
            cgstAmount: $taxBreakdown->cgstAmount,
            sgstAmount: $taxBreakdown->sgstAmount,
            igstAmount: $taxBreakdown->igstAmount,
            grandTotal: $totalBasePrice + $totalGstAmount,
            taxBreakdown: $taxBreakdown
        );
    }
}
