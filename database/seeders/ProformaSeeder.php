<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use App\Models\Invoice;
use App\Services\Invoice\InvoiceService;
use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;

class ProformaSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            $this->command?->warn('No company found. Run CompanySeeder first.');
            return;
        }

        // Check if proformas already exist for this company
        if (Invoice::where('company_id', $company->id)
            ->where('invoice_type', 'proforma')
            ->exists()
        ) {
            $this->command?->info('Proforma invoices already exist for ' . $company->name . '. Skipping.');
            return;
        }

        $admin = User::where('company_id', $company->id)->first();
        $client = Client::where('company_id', $company->id)->first();
        $product = Product::where('company_id', $company->id)->first();

        if (!$admin || !$client || !$product) {
            $this->command?->warn('Missing admin, client, or product. Run UserSeeder, ClientSeeder, ProductSeeder first.');
            return;
        }

        $invoiceService = app(InvoiceService::class);

        // Create a proforma invoice (no stock deduction)
        try {
            $invoice = $invoiceService->createProforma(new InvoiceData(
                company_id: $company->id,
                client_id: $client->id,
                created_by: $admin->id,
                invoice_type: 'proforma',
                gst_mode: 'exclusive',
                invoice_date: now()->format('Y-m-d'),
                due_date: now()->addDays(30)->format('Y-m-d'),
                reference_number: 'PRO-SEED-001',
                discount_type: 'fixed',
                discount_amount: 0,
                shipping_charges: 0,
                commission: 0,
                notes: 'Sample proforma invoice created by ProformaSeeder.',
                terms_and_conditions: 'Payment due in 30 days.',
                payment_terms: 'Net 30',
                logistics_details: ['Standard delivery'],
                estimated_delivery_date: now()->addDays(5)->format('Y-m-d'),
                items: [
                    new InvoiceItemData(
                        name: $product->name,
                        quantity: 5,
                        unit_price: $product->unit_price,
                        description: $product->description ?? null,
                        gst_rate: $product->gst_rate ?? 18.00,
                        discount_type: null,
                        discount_value: 0,
                        taxable_amount: 5 * $product->unit_price,
                    ),
                ],
                status: 'draft',
            ));

            $this->command?->info('✅ Proforma invoice ' . $invoice->invoice_number . ' created for ' . $company->name);
        } catch (\Exception $e) {
            $this->command?->error('❌ Proforma seeding failed: ' . $e->getMessage());
        }
    }
}
