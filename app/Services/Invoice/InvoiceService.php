<?php

namespace App\Services\Invoice;

use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;
use App\DTOs\InvoiceTotals;
use App\Models\Company;
use App\Models\Client;
use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\GST\TaxBreakdownService;
use App\Services\NumberGenerator\InvoiceNumberGenerator;

class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private TaxBreakdownService $taxBreakdownService,
        private InvoiceNumberGenerator $numberGenerator
    ) {}

    /**
     * Create a new proforma invoice
     */
    public function createProforma(InvoiceData $data): Invoice
    {
        // Calculate totals
        $totals = $this->calculateTotals($data);

        // Generate proforma number
        $invoiceNumber = $this->numberGenerator->generateProformaNumber($data->company_id);

        // Create invoice
        return $this->invoiceRepository->create([
            'company_id' => $data->company_id,
            'client_id' => $data->client_id,
            'created_by' => $data->created_by,
            'invoice_number' => $invoiceNumber,
            'invoice_type' => 'proforma',
            'status' => $data->status ?? 'draft',
            'reference_number' => $data->reference_number,
            'invoice_date' => $data->invoice_date,
            'due_date' => $data->due_date,
            'gst_mode' => $data->gst_mode ?? 'exclusive',
            'gst_rate' => $data->gst_rate ?? 18.00,
            'subtotal' => $totals->subtotal,
            'discount_type' => $data->discount_type,
            'discount_amount' => $totals->discountAmount,
            'taxable_amount' => $totals->taxableAmount,
            'cgst_amount' => $totals->taxBreakdown?->cgstAmount ?? 0,
            'sgst_amount' => $totals->taxBreakdown?->sgstAmount ?? 0,
            'igst_amount' => $totals->taxBreakdown?->igstAmount ?? 0,
            'total_gst_amount' => $totals->totalGst,
            'shipping_charges' => $totals->shippingCharges,
            'commission' => $totals->commission,
            'grand_total' => $totals->grandTotal,
            'balance_due' => $totals->grandTotal,
            'notes' => $data->notes,
            'terms_and_conditions' => $data->terms_and_conditions,
            'payment_terms' => $data->payment_terms ?? 'Net 15',
            'logistics_details' => $data->logistics_details,
            'estimated_delivery_date' => $data->estimated_delivery_date,
            'items' => array_map(fn($item) => $item->toArray(), $data->items),
        ]);
    }

    /**
     * Update existing proforma invoice
     */
    public function updateProforma(int $id, InvoiceData $data): Invoice
    {
        $totals = $this->calculateTotals($data);

        return $this->invoiceRepository->update($id, [
            'client_id' => $data->client_id,
            'updated_by' => $data->updated_by,
            'reference_number' => $data->reference_number,
            'invoice_date' => $data->invoice_date,
            'due_date' => $data->due_date,
            'gst_mode' => $data->gst_mode ?? 'exclusive',
            'gst_rate' => $data->gst_rate ?? 18.00,
            'subtotal' => $totals->subtotal,
            'discount_type' => $data->discount_type,
            'discount_amount' => $totals->discountAmount,
            'taxable_amount' => $totals->taxableAmount,
            'cgst_amount' => $totals->taxBreakdown?->cgstAmount ?? 0,
            'sgst_amount' => $totals->taxBreakdown?->sgstAmount ?? 0,
            'igst_amount' => $totals->taxBreakdown?->igstAmount ?? 0,
            'total_gst_amount' => $totals->totalGst,
            'shipping_charges' => $totals->shippingCharges,
            'commission' => $totals->commission,
            'grand_total' => $totals->grandTotal,
            'balance_due' => $totals->grandTotal,
            'notes' => $data->notes,
            'terms_and_conditions' => $data->terms_and_conditions,
            'payment_terms' => $data->payment_terms ?? 'Net 15',
            'logistics_details' => $data->logistics_details,
            'estimated_delivery_date' => $data->estimated_delivery_date,
            'items' => array_map(fn($item) => $item->toArray(), $data->items),
        ]);
    }

    /**
     * Calculate invoice totals from data
     */
    public function calculateTotals(InvoiceData $data): InvoiceTotals
    {
        $subtotal = 0;
        foreach ($data->items as $item) {
            $subtotal += $item->unit_price * $item->quantity;
        }

        $discountAmount = 0;
        if ($data->discount_type === 'fixed') {
            $discountAmount = $data->discount_amount ?? 0;
        } elseif ($data->discount_type === 'percentage') {
            $discountAmount = $subtotal * (($data->discount_amount ?? 0) / 100);
        }
        $afterDiscount = $subtotal - $discountAmount;

        // Let TaxBreakdownService calculate tax on the discounted amount
        $totals = $this->taxBreakdownService->calculateInvoiceTax(
            subtotal: $afterDiscount,          // pass discounted subtotal
            discountPercentage: 0,             // no additional discount
            mode: $data->gst_mode ?? 'exclusive',
            sellerState: Company::find($data->company_id)->state_code ?? '24',
            buyerState: Client::find($data->client_id)->state_code ?? '24',
            gstRate: $data->gst_rate ?? 18.00,
            shippingCharges: $data->shipping_charges ?? 0,
            commission: $data->commission ?? 0,
        );

        // Override subtotal and discountAmount in returned InvoiceTotals
        return new InvoiceTotals(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            taxableAmount: $afterDiscount,
            totalGst: $totals->totalGst,
            cgstAmount: $totals->cgstAmount,
            sgstAmount: $totals->sgstAmount,
            igstAmount: $totals->igstAmount,
            grandTotal: $afterDiscount + $totals->totalGst + ($data->shipping_charges ?? 0) + ($data->commission ?? 0),
            taxBreakdown: $totals->taxBreakdown,
            shippingCharges: $totals->shippingCharges,
            commission: $totals->commission,
        );
    }

    /**
     * Get invoice with relations
     */
    public function getInvoice(int $id, int $companyId): ?Invoice
    {
        return $this->invoiceRepository->findById($id, $companyId);
    }

    /**
     * List invoices for company
     */
    public function listProformas(int $companyId, array $filters = [])
    {
        $filters['type'] = 'proforma';
        return $this->invoiceRepository->getByCompany($companyId, $filters);
    }

    /**
     * Delete invoice
     */
    public function deleteProforma(int $id): bool
    {
        return $this->invoiceRepository->delete($id);
    }
}
