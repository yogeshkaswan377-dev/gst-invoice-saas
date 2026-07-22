<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Services\Invoice\GSTInvoiceService;
use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(GSTInvoiceService::class);

        // Seed for the first company only (or loop for all)
        $company = Company::first();
        if (!$company) return;

        $admin = User::where('company_id', $company->id)->first();
        if (!$admin) return;

        $clients = Client::where('company_id', $company->id)->get();
        // Only use products that have stock > 0 (the fabric products)
        $products = Product::where('company_id', $company->id)
            ->where('stock', '>', 0)
            ->get();

        if ($clients->count() < 2 || $products->count() < 2) {
            echo "⚠️ Not enough clients or products with stock to create sample invoices.\n";
            return;
        }

        // For safety, ensure we use quantities that don't exceed available stock
        // For Meter type, deduct directly; for Piece type, consider consumption_per_piece
        $firstProduct  = $products[0];
        $secondProduct = $products[1] ?? $products[0];

        // Calculate safe quantities
        $qty1 = min(5, floor($firstProduct->stock));  // for Meter type
        if ($firstProduct->stock_deduction_type === 'Piece') {
            $qty1 = min(3, floor($firstProduct->stock / ($firstProduct->consumption_per_piece ?: 1)));
        }

        $qty2 = min(2, floor($secondProduct->stock));
        if ($secondProduct->stock_deduction_type === 'Piece') {
            $qty2 = min(2, floor($secondProduct->stock / ($secondProduct->consumption_per_piece ?: 1)));
        }

        // --- GST Invoice 1 (intra‑state, multiple items) ---
        try {
            $service->createGSTInvoice(new InvoiceData(
                company_id: $company->id,
                client_id: $clients[0]->id,
                created_by: $admin->id,
                invoice_type: 'gst_invoice',
                gst_mode: 'exclusive',
                invoice_date: now()->format('Y-m-d'),
                due_date: now()->addDays(15)->format('Y-m-d'),
                discount_type: 'fixed',
                discount_amount: 500,
                notes: 'Sample GST invoice',
                status: 'sent',
                items: [
                    new InvoiceItemData(
                        name: $firstProduct->name,
                        quantity: $qty1,
                        unit_price: $firstProduct->unit_price,
                        hsn_sac_code: $firstProduct->hsn_sac_code,
                        gst_rate: $firstProduct->gst_rate,
                        taxable_amount: $qty1 * $firstProduct->unit_price,
                        productId: $firstProduct->id
                    ),
                    new InvoiceItemData(
                        name: $secondProduct->name,
                        quantity: $qty2,
                        unit_price: $secondProduct->unit_price,
                        hsn_sac_code: $secondProduct->hsn_sac_code,
                        gst_rate: $secondProduct->gst_rate,
                        taxable_amount: $qty2 * $secondProduct->unit_price,
                        productId: $secondProduct->id
                    ),
                ]
            ));
        } catch (\Exception $e) {
            echo "⚠️ GST Invoice 1 failed: " . $e->getMessage() . "\n";
        }

        // --- Proforma Invoice 1 ---
        // Proforma invoices do NOT require stock, so we can safely use any product
        $proformaProduct = Product::where('company_id', $company->id)->first();
        if ($proformaProduct) {
            try {
                $service->createGSTInvoice(new InvoiceData(
                    company_id: $company->id,
                    client_id: $clients[1]->id ?? $clients[0]->id,
                    created_by: $admin->id,
                    invoice_type: 'proforma',
                    gst_mode: 'exclusive',
                    invoice_date: now()->format('Y-m-d'),
                    due_date: now()->addDays(30)->format('Y-m-d'),
                    notes: 'Sample proforma invoice',
                    status: 'draft',
                    items: [
                        new InvoiceItemData(
                            name: $proformaProduct->name,
                            quantity: 5,
                            unit_price: $proformaProduct->unit_price,
                            gst_rate: $proformaProduct->gst_rate,
                            productId: $proformaProduct->id
                        ),
                    ]
                ));
            } catch (\Exception $e) {
                echo "⚠️ Proforma Invoice failed: " . $e->getMessage() . "\n";
            }
        }

        echo "✅ Sample invoices seeded successfully.\n";
    }
}
