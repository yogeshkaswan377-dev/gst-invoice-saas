<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Services\Invoice\GSTInvoiceService;
use App\DTOs\InvoiceData;
use App\DTOs\InvoiceItemData;

class SampleInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(GSTInvoiceService::class);
        $companyId = 1; // <-- Change if your company ID is different
        $createdBy = 1; // <-- Change if user ID is different

        $getClient = fn (string $email) => Client::where('email', $email)->firstOrFail();

        // =========================================
        // GST INVOICE 1 - Reliance Digital
        // =========================================
        $client1 = $getClient('accounts@reliancedigital.com');
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client1->id,
            created_by: $createdBy,
            invoice_type: 'gst_invoice',
            gst_mode: 'exclusive',
            gst_rate: 28, // highest rate among items; service will compute per item
            invoice_date: '2026-06-15',
            due_date: '2026-06-30',
            reference_number: 'PO-RD-2026-101',
            discount_type: 'fixed',
            discount_amount: 3000,
            notes: 'Warranty: 1 year on TV, 3 months on installation.',
            status: 'sent',
            items: [
                new InvoiceItemData(name: '65" OLED Smart TV', quantity: 2, unit_price: 120000, hsn_sac_code: '8528', gst_rate: 28),
                new InvoiceItemData(name: 'Installation & Setup Charges', quantity: 1, unit_price: 5000, hsn_sac_code: '9987', gst_rate: 18),
            ],
        ));

        // =========================================
        // GST INVOICE 2 - Infosys
        // =========================================
        $client2 = $getClient('billing@infosys.com');
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client2->id,
            created_by: $createdBy,
            invoice_type: 'gst_invoice',
            gst_mode: 'exclusive',
            gst_rate: 18,
            invoice_date: '2026-06-20',
            due_date: '2026-07-05',
            reference_number: 'INFY/2026/IT-442',
            discount_type: 'percentage',
            discount_amount: 5,
            notes: 'Milestone payment. 50% on delivery, 50% post UAT.',
            status: 'draft',
            items: [
                new InvoiceItemData(name: 'Custom Software Development (300 hrs)', quantity: 1, unit_price: 450000, hsn_sac_code: '9983', gst_rate: 18),
                new InvoiceItemData(name: 'Cloud Infrastructure Setup', quantity: 1, unit_price: 85000, hsn_sac_code: '998315', gst_rate: 18),
            ],
        ));

        // =========================================
        // GST INVOICE 3 - Delhi Distributors
        // =========================================
        $client3 = $getClient('orders@delhidist.com');
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client3->id,
            created_by: $createdBy,
            invoice_type: 'gst_invoice',
            gst_mode: 'exclusive',
            gst_rate: 18,
            invoice_date: '2026-06-25',
            due_date: '2026-07-10',
            shipping_charges: 1500,
            notes: 'Delivery within 3 working days.',
            status: 'sent',
            items: [
                new InvoiceItemData(name: 'Electrical Switches (Modular)', quantity: 50, unit_price: 180, hsn_sac_code: '8536', gst_rate: 18),
                new InvoiceItemData(name: 'Copper Wires (90m coil)', quantity: 10, unit_price: 2200, hsn_sac_code: '8544', gst_rate: 18),
            ],
        ));

        // =========================================
        // PROFORMA 1 - Reliance Digital
        // =========================================
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client1->id,
            created_by: $createdBy,
            invoice_type: 'proforma',
            gst_mode: 'exclusive',
            gst_rate: 28,
            invoice_date: '2026-06-10',
            due_date: '2026-06-25',
            reference_number: 'PROF-RD-2026-007',
            discount_type: 'fixed',
            discount_amount: 200000,
            notes: 'Proforma valid for 30 days. Prices subject to change after 30 days.',
            status: 'draft',
            items: [
                new InvoiceItemData(name: 'Bulk Order – 100 Smart TVs (Quote)', quantity: 100, unit_price: 110000, hsn_sac_code: '8528', gst_rate: 28),
            ],
        ));

        // =========================================
        // PROFORMA 2 - Suresh Kumar (Individual)
        // =========================================
        $client4 = $getClient('suresh.kumar@gmail.com');
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client4->id,
            created_by: $createdBy,
            invoice_type: 'proforma',
            gst_mode: 'exclusive',
            gst_rate: 18,
            invoice_date: '2026-06-18',
            due_date: '2026-07-03',
            notes: 'B2C – GST inclusive. Advance payment required.',
            status: 'draft',
            items: [
                new InvoiceItemData(name: 'Interior Design Consultation', quantity: 1, unit_price: 35000, hsn_sac_code: '9983', gst_rate: 18),
                new InvoiceItemData(name: 'Furniture Set (Sofa + Table)', quantity: 1, unit_price: 65000, hsn_sac_code: '9403', gst_rate: 12),
            ],
        ));

        // =========================================
        // PROFORMA 3 - Global Trade LLC (Export)
        // =========================================
        $client5 = $getClient('orders@globaltrade.com');
        $service->createGSTInvoice(new InvoiceData(
            company_id: $companyId,
            client_id: $client5->id,
            created_by: $createdBy,
            invoice_type: 'proforma',
            gst_mode: 'exclusive',
            gst_rate: 0,
            invoice_date: '2026-06-22',
            due_date: '2026-07-07',
            reference_number: 'EXP-2026/GT-009',
            notes: 'Export under LUT. No IGST charged. Shipment FOB Mumbai.',
            status: 'draft',
            items: [
                new InvoiceItemData(name: 'Handwoven Carpets (Export)', quantity: 20, unit_price: 8500, hsn_sac_code: '5701', gst_rate: 0),
                new InvoiceItemData(name: 'Packaging & Freight', quantity: 1, unit_price: 15000, hsn_sac_code: '9988', gst_rate: 0),
            ],
        ));

        echo "✅ 3 GST Invoices + 3 Proformas created successfully!\n";
    }
}