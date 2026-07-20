<?php

namespace App\Services\Invoice;

use App\DTOs\InvoiceData;
use App\DTOs\InvoiceTotals;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Services\GST\GSTCalculationService;
use App\Services\GST\TaxBreakdownService;
use App\Services\InvoiceStockService;
use App\Services\NumberGenerator\InvoiceNumberGenerator;
use Illuminate\Support\Facades\DB;

class GSTInvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private TaxBreakdownService $taxBreakdownService,
        private InvoiceNumberGenerator $numberGenerator,
        private InvoiceStockService $invoiceStockService
    ) {}

    /**
     * Create a new GST invoice
     */
    public function createGSTInvoice(InvoiceData $data): Invoice
    {
        $totals = $this->calculateGSTTotals($data);
        $invoiceNumber = $this->numberGenerator->generateInvoiceNumber($data->company_id);

        $invoice = DB::transaction(function () use ($data, $totals, $invoiceNumber) {
            // Create invoice record (without items JSON)
            $invoice = Invoice::create([
                'company_id' => $data->company_id,
                'client_id' => $data->client_id,
                'created_by' => $data->created_by,
                'invoice_number' => $invoiceNumber,
                'invoice_type' => 'gst_invoice',
                'status' => $data->status ?? 'draft',
                'reference_number' => $data->reference_number,
                'invoice_date' => $data->invoice_date,
                'due_date' => $data->due_date,
                'gst_mode' => $data->gst_mode,
                'gst_rate' => $data->gst_rate ?? 18.00,
                'place_of_supply' => $this->getPlaceOfSupply($data->company_id, $data->client_id),
                'place_of_supply_state_code' => Client::find($data->client_id)->state_code ?? null,
                'reverse_charge' => $data->reverse_charge ?? false,
                'subtotal' => $totals->subtotal,
                'discount_type' => $data->discount_type,
                'discount_amount' => $totals->discountAmount,
                'taxable_amount' => $totals->taxableAmount,
                'cgst_amount' => $totals->cgstAmount ?? 0,
                'sgst_amount' => $totals->sgstAmount ?? 0,
                'igst_amount' => $totals->igstAmount ?? 0,
                'total_gst_amount' => $totals->totalGst,
                'shipping_charges' => $totals->shippingCharges,
                'commission' => $totals->commission,
                'grand_total' => $totals->grandTotal,
                'paid_amount' => 0,
                'balance_due' => $totals->grandTotal,
                'notes' => $data->notes,
                'terms_and_conditions' => $data->terms_and_conditions,
                'payment_terms' => $data->payment_terms ?? 'Net 15',
            ]);

            // Create invoice items
            foreach ($data->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->productId,
                    'name' => $item->name,
                    'description' => $item->description,
                    'hsn_sac_code' => $item->hsn_sac_code,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'gst_rate' => $item->gst_rate,
                    'taxable_amount' => $item->taxable_amount,
                    'cgst_amount' => 0,   // will be updated if needed, or set properly
                    'sgst_amount' => 0,
                    'igst_amount' => 0,
                    'line_total' => $item->unit_price * $item->quantity,
                ]);
            }

            // Validate stock
            $stockErrors = $this->invoiceStockService->validateStock($invoice);
            if (!empty($stockErrors)) {
                throw new \Exception(implode(' ', $stockErrors));
            }

            // Deduct stock
            $this->invoiceStockService->deductStock($invoice);

            return $invoice;
        });

        return $invoice;
    }

    /**
     * Update GST invoice
     */
    public function updateGSTInvoice(int $id, InvoiceData $data): Invoice
    {
        $oldInvoice = Invoice::with('items')->findOrFail($id);
        $totals = $this->calculateGSTTotals($data);

        $updatedInvoice = DB::transaction(function () use ($oldInvoice, $data, $totals) {
            // Update invoice header
            $oldInvoice->update([
                'client_id' => $data->client_id,
                'updated_by' => $data->updated_by,
                'reference_number' => $data->reference_number,
                'invoice_date' => $data->invoice_date,
                'due_date' => $data->due_date,
                'gst_mode' => $data->gst_mode,
                'place_of_supply' => $this->getPlaceOfSupply($data->company_id, $data->client_id),
                // ... copy all header fields from create above ...
                'subtotal' => $totals->subtotal,
                'discount_amount' => $totals->discountAmount,
                'taxable_amount' => $totals->taxableAmount,
                'cgst_amount' => $totals->cgstAmount ?? 0,
                'sgst_amount' => $totals->sgstAmount ?? 0,
                'igst_amount' => $totals->igstAmount ?? 0,
                'total_gst_amount' => $totals->totalGst,
                'shipping_charges' => $totals->shippingCharges,
                'commission' => $totals->commission,
                'grand_total' => $totals->grandTotal,
                'balance_due' => $totals->grandTotal - $oldInvoice->paid_amount,
                'notes' => $data->notes,
                'terms_and_conditions' => $data->terms_and_conditions,
                'payment_terms' => $data->payment_terms ?? 'Net 15',
            ]);

            // Delete old items and recreate
            $oldInvoice->items()->delete();
            foreach ($data->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $oldInvoice->id,
                    'product_id' => $item->productId,
                    'name' => $item->name,
                    'description' => $item->description,
                    'hsn_sac_code' => $item->hsn_sac_code,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'gst_rate' => $item->gst_rate,
                    'taxable_amount' => $item->taxable_amount,
                    'cgst_amount' => 0,   // will be updated if needed, or set properly
                    'sgst_amount' => 0,
                    'igst_amount' => 0,
                    'line_total' => $item->unit_price * $item->quantity,
                    // ... same as create
                ]);
            }

            // Adjust stock: restore old stock + deduct new
            $newInvoice = $oldInvoice->fresh()->load('items');
            $this->invoiceStockService->adjustStockForEdit($oldInvoice, $newInvoice);

            return $newInvoice;
        });

        return $updatedInvoice;
    }



    /**
     * Calculate GST totals with proper state logic
     */
    public function calculateGSTTotals(InvoiceData $data): InvoiceTotals
    {
        $subtotal = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $totalIgst = 0;
        $totalTaxable = 0;

        $sellerState = Company::find($data->company_id)->state_code ?? '24';
        $buyerState = Client::find($data->client_id)->state_code ?? '24';
        $isIntraState = $sellerState === $buyerState;

        foreach ($data->items as $item) {
            $lineTotal = $item->unit_price * $item->quantity;
            $subtotal += $lineTotal;

            $gstRate = $item->gst_rate ?? 18;   // per item GST rate
            $gstAmount = $lineTotal * ($gstRate / 100);

            if ($isIntraState) {
                $totalCgst += $gstAmount / 2;
                $totalSgst += $gstAmount / 2;
            } else {
                $totalIgst += $gstAmount;
            }
        }

        // Apply discount to subtotal
        $discountAmount = 0;
        $afterDiscount = $subtotal;
        if ($data->discount_type === 'fixed') {
            $discountAmount = $data->discount_amount ?? 0;
            $afterDiscount -= $discountAmount;
        } elseif ($data->discount_type === 'percentage') {
            $discountPct = $data->discount_amount ?? 0;
            $discountAmount = $subtotal * ($discountPct / 100);
            $afterDiscount -= $discountAmount;
        }

        // Recalculate GST proportionally to the discounted subtotal (if discount applied)
        if ($subtotal > 0) {
            $ratio = $afterDiscount / $subtotal;
            $totalCgst *= $ratio;
            $totalSgst *= $ratio;
            $totalIgst *= $ratio;
        }

        $totalGst = $totalCgst + $totalSgst + $totalIgst;
        $grandTotal = $afterDiscount + $totalGst + ($data->shipping_charges ?? 0) + ($data->commission ?? 0);

        return new InvoiceTotals(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            taxableAmount: $afterDiscount,
            totalGst: $totalGst,
            cgstAmount: $totalCgst,
            sgstAmount: $totalSgst,
            igstAmount: $totalIgst,
            grandTotal: $grandTotal,
            shippingCharges: $data->shipping_charges ?? 0,
            commission: $data->commission ?? 0,
        );
    }
    /**
     * Determine place of supply type
     */
    private function getPlaceOfSupply(int $companyId, int $clientId): string
    {
        $companyState = Company::find($companyId)->state_code;
        $clientState = Client::find($clientId)->state_code;
        return $companyState === $clientState ? 'intra_state' : 'inter_state';
    }

    /**
     * Recalculate invoice with new GST mode (exclusive/inclusive)
     */
    public function recalculateWithMode(int $invoiceId, string $newMode): Invoice
    {
        $invoice = $this->invoiceRepository->findById($invoiceId, $invoice->company_id);

        // If mode changes, recalculate items
        if ($invoice->gst_mode !== $newMode) {
            // Update mode and recalculate
            $invoice->update(['gst_mode' => $newMode]);
            // TODO: Recalculate all items with new mode
        }

        return $invoice->fresh();
    }

    /**
     * Get tax breakdown for display
     */
    public function getTaxBreakdown(int $invoiceId): array
    {
        $invoice = Invoice::with('items')->find($invoiceId);

        return [
            'tax_type' => $invoice->igst_amount > 0 ? 'igst' : 'cgst_sgst',
            'cgst' => $invoice->cgst_amount,
            'sgst' => $invoice->sgst_amount,
            'igst' => $invoice->igst_amount,
            'total_gst' => $invoice->total_gst_amount,
            'mode' => $invoice->gst_mode,
            'rate' => $invoice->gst_rate,
            'subtotal' => $invoice->subtotal,
            'taxable' => $invoice->taxable_amount,
            'grand_total' => $invoice->grand_total,
        ];
    }

    /**
     * Get invoice with relations
     */
    public function getInvoice(int $id, int $companyId): ?Invoice
    {
        return $this->invoiceRepository->findById($id, $companyId);
    }

    /**
     * List GST invoices
     */
    public function listGSTInvoices(int $companyId, array $filters = [])
    {
        $filters['type'] = 'gst_invoice';
        return $this->invoiceRepository->getByCompany($companyId, $filters);
    }

    /**
     * Delete invoice
     */
    public function deleteGSTInvoice(int $id): bool
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        DB::transaction(function () use ($invoice) {
            $this->invoiceStockService->restoreStock($invoice);
            $invoice->delete();
        });
        return true;
    }
}
