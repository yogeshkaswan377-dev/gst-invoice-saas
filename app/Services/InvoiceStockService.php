<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\DTOs\StockAdjustmentData;
use Illuminate\Support\Facades\DB;

class InvoiceStockService
{
    public function __construct(
        private StockService $stockService,
        private ProductService $productService
    ) {}

    /**
     * Validate stock for all items in an invoice before saving.
     * Returns array of error messages (empty if all OK).
     */
    public function validateStock(Invoice $invoice): array
    {
        $errors = [];

        // Only for GST invoices (or you can allow all types)
        if ($invoice->invoice_type !== 'gst_invoice') {
            return [];
        }

        foreach ($invoice->items as $item) {
            if (!$item->product_id) continue; // not linked to a product

            $product = $item->product;
            if (!$product) continue;

            $requiredStock = $this->calculateRequiredStock($product, $item->quantity);

            if ($product->stock_deduction_type === 'None') {
                continue; // No stock tracking for this product
            }

            $availableStock = $product->stock;

            if ($requiredStock > $availableStock) {
                $errors[] = sprintf(
                    'Insufficient stock for "%s". Available: %s %s, Required: %s %s.',
                    $product->name,
                    $availableStock,
                    $product->stock_unit,
                    $requiredStock,
                    $product->stock_unit
                );
            }
        }

        return $errors;
    }

    /**
     * Deduct stock for all items after a new invoice is created.
     */
    public function deductStock(Invoice $invoice): void
    {
        if ($invoice->invoice_type !== 'gst_invoice') return;

        DB::transaction(function () use ($invoice) {
            foreach ($invoice->items as $item) {
                if (!$item->product_id) continue;

                $product = $item->product;
                if ($product->stock_deduction_type === 'None') continue;
                $requiredStock = $this->calculateRequiredStock($product, $item->quantity);

                // Save consumed_stock to item (if not already)
                $item->update(['consumed_stock' => $requiredStock]);

                // Deduct stock
                $this->stockService->adjustStock(new StockAdjustmentData(
                    productId: $product->id,
                    companyId: $invoice->company_id,
                    userId: $invoice->created_by ?? auth()->id(),
                    adjustmentType: 'deduct',
                    quantity: $requiredStock,
                    remarks: 'Invoice ' . $invoice->invoice_number
                ));
            }
        });
    }

    /**
     * Adjust stock when an invoice is edited.
     * $oldInvoice = original state before update, $newInvoice = after update.
     */
    public function adjustStockForEdit(Invoice $oldInvoice, Invoice $newInvoice): void
    {
        if ($oldInvoice->invoice_type !== 'gst_invoice') return;

        DB::transaction(function () use ($oldInvoice, $newInvoice) {
            // Restore old stock
            foreach ($oldInvoice->items as $oldItem) {
                if ($oldItem->product_id && $oldItem->consumed_stock > 0) {
                    $this->stockService->adjustStock(new StockAdjustmentData(
                        productId: $oldItem->product_id,
                        companyId: $oldInvoice->company_id,
                        userId: auth()->id(),
                        adjustmentType: 'add',
                        quantity: $oldItem->consumed_stock,
                        remarks: 'Invoice edited (restore) ' . $oldInvoice->invoice_number
                    ));
                }
            }

            // Deduct new stock
            foreach ($newInvoice->items as $newItem) {
                if (!$newItem->product_id) continue;

                $product = Product::find($newItem->product_id);
                if ($product->stock_deduction_type === 'None') continue;
                $requiredStock = $this->calculateRequiredStock($product, $newItem->quantity);

                $newItem->update(['consumed_stock' => $requiredStock]);

                $this->stockService->adjustStock(new StockAdjustmentData(
                    productId: $product->id,
                    companyId: $newInvoice->company_id,
                    userId: auth()->id(),
                    adjustmentType: 'deduct',
                    quantity: $requiredStock,
                    remarks: 'Invoice edited (new) ' . $newInvoice->invoice_number
                ));
            }
        });
    }

    /**
     * Restore stock when an invoice is deleted.
     */
    public function restoreStock(Invoice $invoice): void
    {
        if ($invoice->invoice_type !== 'gst_invoice') return;

        DB::transaction(function () use ($invoice) {
            foreach ($invoice->items as $item) {
                if ($item->product_id && $item->consumed_stock > 0) {
                    $this->stockService->adjustStock(new StockAdjustmentData(
                        productId: $item->product_id,
                        companyId: $invoice->company_id,
                        userId: auth()->id(),
                        adjustmentType: 'add',
                        quantity: $item->consumed_stock,
                        remarks: 'Invoice deleted ' . $invoice->invoice_number
                    ));
                }
            }
        });
    }

    /**
     * Calculate how many stock units are needed for a given invoice quantity.
     */
    private function calculateRequiredStock(Product $product, float $invoiceQuantity): float
    {
        return match ($product->stock_deduction_type) {
            'Meter', 'Kg', 'Roll', 'Box', 'Custom' => $invoiceQuantity,
            'Piece' => $invoiceQuantity * ($product->consumption_per_piece ?? 1),
            default => $invoiceQuantity,
        };
    }
}
