<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. COMPANIES (same as before, no change)
        // ============================================
        $company1 = Company::where('email', 'contact@demobusiness.com')->first();
        if (!$company1) {
            $company1 = Company::create([
                'name' => 'Demo Business Pvt Ltd',
                'email' => 'contact@demobusiness.com',
                'phone' => '9876543210',
                'gstin' => '27ABCDE1234F1Z5',
                'state_code' => '27',
                'state' => 'Maharashtra',
                'address_line_1' => '123, Business Park, Andheri East',
                'city' => 'Mumbai',
                'pincode' => '400093',
                'country' => 'India',
                'subscription_plan' => 'trial',
                'is_active' => 1,
            ]);
        }

        $company2 = Company::firstOrCreate(
            ['email' => 'info@techsolutions.com'],
            [
                'name' => 'Tech Solutions India',
                'phone' => '9988776655',
                'gstin' => '29AABCT1234E1Z6',
                'state_code' => '29',
                'state' => 'Karnataka',
                'address_line_1' => '456, Tech Park, Whitefield',
                'city' => 'Bangalore',
                'pincode' => '560066',
                'country' => 'India',
                'subscription_plan' => 'basic',
                'is_active' => 1,
            ]
        );

        $company3 = Company::firstOrCreate(
            ['email' => 'info@gujarattextiles.com'],
            [
                'name' => 'Gujarat Textiles',
                'phone' => '9876541230',
                'gstin' => '24ABCDE5678F1Z9',
                'state_code' => '24',
                'state' => 'Gujarat',
                'address_line_1' => '789, Textile Market',
                'city' => 'Ahmedabad',
                'pincode' => '380001',
                'country' => 'India',
                'subscription_plan' => 'premium',
                'is_active' => 1,
            ]
        );

        // ============================================
        // 2. USERS (unchanged)
        // ============================================
        $admin1 = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'company_id' => $company1->id,
                'current_company_id' => $company1->id,
                'phone' => '9876543210',
            ]
        );
        if (!$admin1->hasRole('owner')) {
            $admin1->assignRole('owner', $company1->id);
        }

        $owner2 = User::firstOrCreate(
            ['email' => 'rahul@techsolutions.com'],
            [
                'name' => 'Rahul Sharma',
                'password' => Hash::make('password'),
                'company_id' => $company2->id,
                'current_company_id' => $company2->id,
                'phone' => '9988776655',
            ]
        );
        if (!$owner2->hasRole('owner')) {
            $owner2->assignRole('owner', $company2->id);
        }

        $owner3 = User::firstOrCreate(
            ['email' => 'amit@gujarattextiles.com'],
            [
                'name' => 'Amit Patel',
                'password' => Hash::make('password'),
                'company_id' => $company3->id,
                'current_company_id' => $company3->id,
                'phone' => '9876541230',
            ]
        );
        if (!$owner3->hasRole('owner')) {
            $owner3->assignRole('owner', $company3->id);
        }

        $staff1 = User::firstOrCreate(
            ['email' => 'priya@demobusiness.com'],
            [
                'name' => 'Priya Singh',
                'password' => Hash::make('password'),
                'company_id' => $company1->id,
                'current_company_id' => $company1->id,
                'phone' => '9876512345',
            ]
        );
        if (!$staff1->hasRole('staff')) {
            $staff1->assignRole('staff', $company1->id);
        }

        $staff2 = User::firstOrCreate(
            ['email' => 'vikram@demobusiness.com'],
            [
                'name' => 'Vikram Desai',
                'password' => Hash::make('password'),
                'company_id' => $company1->id,
                'current_company_id' => $company1->id,
                'phone' => '9876512346',
            ]
        );
        if (!$staff2->hasRole('admin')) {
            $staff2->assignRole('admin', $company1->id);
        }

        // ============================================
        // 3. CLIENTS (unchanged)
        // ============================================
        $clientData = [
            [
                'company_id' => $company1->id,
                'client_type' => 'business',
                'name' => 'Reliance Digital',
                'company_name' => 'Reliance Digital Ltd',
                'email' => 'accounts@reliancedigital.com',
                'phone' => '022-45678901',
                'gstin' => '27AAACR1234E1Z5',
                'state_code' => '27',
                'state_name' => 'Maharashtra',
                'state' => 'Maharashtra',
                'address_line_1' => 'Reliance Corporate Park',
                'city' => 'Navi Mumbai',
                'pincode' => '400701',
                'country' => 'India',
                'place_of_supply' => 'intra_state',
                'status' => 'active',
                'is_active' => 1,
            ],
            [
                'company_id' => $company1->id,
                'client_type' => 'business',
                'name' => 'Infosys Technologies',
                'company_name' => 'Infosys Ltd',
                'email' => 'billing@infosys.com',
                'phone' => '080-39876000',
                'gstin' => '29AAACI1234E1Z7',
                'state_code' => '29',
                'state_name' => 'Karnataka',
                'state' => 'Karnataka',
                'address_line_1' => 'Electronics City',
                'city' => 'Bangalore',
                'pincode' => '560100',
                'country' => 'India',
                'place_of_supply' => 'inter_state',
                'status' => 'active',
                'is_active' => 1,
            ],
            [
                'company_id' => $company1->id,
                'client_type' => 'individual',
                'name' => 'Suresh Kumar',
                'email' => 'suresh.kumar@gmail.com',
                'phone' => '9812345678',
                'state_code' => '27',
                'state_name' => 'Maharashtra',
                'state' => 'Maharashtra',
                'address_line_1' => '42, Palm Street, Bandra West',
                'city' => 'Mumbai',
                'pincode' => '400050',
                'country' => 'India',
                'place_of_supply' => 'intra_state',
                'status' => 'active',
                'is_active' => 1,
            ],
            [
                'company_id' => $company1->id,
                'client_type' => 'business',
                'name' => 'Delhi Distributors',
                'company_name' => 'Delhi Distributors Pvt Ltd',
                'email' => 'orders@delhidist.com',
                'phone' => '011-23456789',
                'gstin' => '07AAACD1234E1Z8',
                'state_code' => '07',
                'state_name' => 'Delhi',
                'state' => 'Delhi',
                'address_line_1' => '15, Okhla Industrial Area',
                'city' => 'New Delhi',
                'pincode' => '110020',
                'country' => 'India',
                'place_of_supply' => 'inter_state',
                'status' => 'active',
                'is_active' => 1,
            ],
            [
                'company_id' => $company1->id,
                'client_type' => 'export',
                'name' => 'Global Trade LLC',
                'company_name' => 'Global Trade LLC',
                'email' => 'orders@globaltrade.com',
                'phone' => '+1-555-0123',
                'state_code' => '99',
                'state_name' => 'Outside India',
                'state' => 'Outside India',
                'address_line_1' => '123, Main Street',
                'city' => 'New York',
                'pincode' => '10001',
                'country' => 'USA',
                'place_of_supply' => 'export',
                'status' => 'active',
                'is_active' => 1,
            ],
        ];

        $clientModels = [];
        foreach ($clientData as $data) {
            $clientModels[] = Client::firstOrCreate(
                ['email' => $data['email'], 'company_id' => $company1->id],
                $data
            );
        }

        // ============================================
        // 4. PRODUCTS (UPDATED with new stock fields)
        // ============================================
        // ---- Service products (stock = 0, Meter type as default) ----
        $serviceProducts = [
            ['item_no' => '10001', 'name' => 'Web Development Service', 'hsn_sac_code' => '998313', 'unit_price' => 50000, 'gst_rate' => 18, 'unit' => 'project'],
            ['item_no' => '10002', 'name' => 'IT Consulting', 'hsn_sac_code' => '998314', 'unit_price' => 25000, 'gst_rate' => 18, 'unit' => 'hour'],
            ['item_no' => '10003', 'name' => 'Cloud Hosting - Monthly', 'hsn_sac_code' => '998315', 'unit_price' => 5000, 'gst_rate' => 18, 'unit' => 'month'],
            ['item_no' => '10004', 'name' => 'Software License', 'hsn_sac_code' => '85238020', 'unit_price' => 150000, 'gst_rate' => 18, 'unit' => 'license'],
            ['item_no' => '10005', 'name' => 'Hardware Setup', 'hsn_sac_code' => '84713010', 'unit_price' => 35000, 'gst_rate' => 28, 'unit' => 'setup'],
            ['item_no' => '10006', 'name' => 'Annual Maintenance', 'hsn_sac_code' => '9987', 'unit_price' => 12000, 'gst_rate' => 18, 'unit' => 'year'],
        ];

        $productModels = [];
        foreach ($serviceProducts as $data) {
            $productModels[] = Product::firstOrCreate(
                ['name' => $data['name'], 'company_id' => $company1->id],
                array_merge($data, [
                    'stock' => 0,
                    'stock_unit' => 'Mtr',
                    'stock_deduction_type' => 'Meter',   // irrelevant for services
                    'consumption_per_piece' => null,
                    'minimum_stock' => 0,
                    'selling_price' => $data['unit_price'],  // same as unit price for services
                ])
            );
        }

        // ---- Physical fabric products (stock in meters, Piece type has consumption) ----
        $fabricProducts = [
            [
                'item_no' => '20001',
                'name' => 'Cotton White Fabric',
                'hsn_sac_code' => '5208',
                'unit_price' => 120.00,
                'gst_rate' => 5,
                'unit' => 'Mtr',
                'stock' => 500.00,
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Meter',
                'consumption_per_piece' => null,
                'minimum_stock' => 50,
                'selling_price' => 120.00,
            ],
            [
                'item_no' => '20002',
                'name' => 'Formal Shirt (Piece)',
                'hsn_sac_code' => '6205',
                'unit_price' => 899.00,
                'gst_rate' => 12,
                'unit' => 'Pcs',
                'stock' => 500.00,          // stock in meters of fabric available
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Piece',
                'consumption_per_piece' => 1.60,   // 1.6 meter per shirt
                'minimum_stock' => 20,
                'selling_price' => 899.00,
            ],
            [
                'item_no' => '20003',
                'name' => 'Kurta (Piece)',
                'hsn_sac_code' => '6211',
                'unit_price' => 1299.00,
                'gst_rate' => 5,
                'unit' => 'Pcs',
                'stock' => 500.00,
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Piece',
                'consumption_per_piece' => 2.40,
                'minimum_stock' => 15,
                'selling_price' => 1299.00,
            ],
        ];

        foreach ($fabricProducts as $data) {
            $productModels[] = Product::firstOrCreate(
                ['name' => $data['name'], 'company_id' => $company1->id],
                $data
            );
        }

        // ============================================
        // 5. INVOICES (unchanged - skips if already present)
        // ============================================

        if (Invoice::where('company_id', $company1->id)->count() > 0) {
            echo "\n⚠️  Invoices already exist for Demo Business. Skipping invoice seeding.\n";
            return;
        }

        // ---- All invoice creation code remains exactly as before ----
        // (using $clientModels[0], $clientModels[1] ...)
        // I'm not repeating it here for brevity; the rest of the file is identical to your existing one.
        // -----------------------------------------------------------------
        // ... (copy the entire invoice creation block from your old seeder)
        // -----------------------------------------------------------------

        // For completeness, I'll include the exact same invoice block that was in your file.
        // Ensure it's present.
        // (I'll put a placeholder comment, but in the real file you must keep the original invoice seeding code.)
        // ============================================
        // 5. INVOICES (Demo Business) — FIXED COLUMNS
        // ============================================

        // Only seed if no invoices exist for company1
        if (Invoice::where('company_id', $company1->id)->count() > 0) {
            echo "\n⚠️  Invoices already exist for Demo Business. Skipping invoice seeding.\n";
            return;
        }

        // --- Proforma Invoice 1 - Draft ---
        $proforma1 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[0]->id,
            'invoice_number' => 'PRO-2026-00001',
            'invoice_type' => 'proforma',
            'invoice_date' => '2026-06-10',
            'due_date' => '2026-07-10',
            'subtotal' => 50000,
            'discount_amount' => 0,
            'taxable_amount' => 50000,
            'total_gst_amount' => 9000,
            'cgst_amount' => 4500,
            'sgst_amount' => 4500,
            'igst_amount' => 0,
            'grand_total' => 59000,
            'paid_amount' => 0,
            'balance_due' => 59000,
            'status' => 'draft',
            'place_of_supply' => 'intra_state',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $proforma1->id,
            'name' => $productModels[0]->name,
            'hsn_sac_code' => $productModels[0]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 50000,
            'gst_rate' => 18,
            'taxable_amount' => 50000,
            'cgst_amount' => 4500,
            'sgst_amount' => 4500,
            'line_total' => 59000,
        ]);

        // --- Proforma Invoice 2 - Sent (Inter-state) ---
        $proforma2 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[1]->id,
            'invoice_number' => 'PRO-2026-00002',
            'invoice_type' => 'proforma',
            'invoice_date' => '2026-06-12',
            'due_date' => '2026-07-12',
            'subtotal' => 25000,
            'discount_amount' => 0,
            'taxable_amount' => 25000,
            'total_gst_amount' => 4500,
            'igst_amount' => 4500,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'grand_total' => 29500,
            'paid_amount' => 0,
            'balance_due' => 29500,
            'status' => 'sent',
            'place_of_supply' => 'inter_state',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $proforma2->id,
            'name' => $productModels[1]->name,
            'hsn_sac_code' => $productModels[1]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 25000,
            'gst_rate' => 18,
            'taxable_amount' => 25000,
            'igst_amount' => 4500,
            'line_total' => 29500,
        ]);

        // --- GST Invoice 1 - Paid ---
        $gst1 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[0]->id,
            'invoice_number' => 'INV-2026-00001',
            'invoice_type' => 'gst_invoice',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-06-15',
            'paid_date' => '2026-06-10',
            'subtotal' => 150000,
            'discount_amount' => 0,
            'taxable_amount' => 150000,
            'total_gst_amount' => 27000,
            'cgst_amount' => 13500,
            'sgst_amount' => 13500,
            'igst_amount' => 0,
            'grand_total' => 177000,
            'paid_amount' => 177000,
            'balance_due' => 0,
            'status' => 'paid',
            'place_of_supply' => 'intra_state',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $gst1->id,
            'name' => $productModels[3]->name,
            'hsn_sac_code' => $productModels[3]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 150000,
            'gst_rate' => 18,
            'taxable_amount' => 150000,
            'cgst_amount' => 13500,
            'sgst_amount' => 13500,
            'line_total' => 177000,
        ]);

        // --- GST Invoice 2 - Sent (Inter-state, 28% GST) ---
        $gst2 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[3]->id,
            'invoice_number' => 'INV-2026-00002',
            'invoice_type' => 'gst_invoice',
            'invoice_date' => '2026-06-05',
            'due_date' => '2026-06-20',
            'subtotal' => 35000,
            'discount_amount' => 0,
            'taxable_amount' => 35000,
            'total_gst_amount' => 9800,
            'igst_amount' => 9800,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'grand_total' => 44800,
            'paid_amount' => 0,
            'balance_due' => 44800,
            'status' => 'sent',
            'place_of_supply' => 'inter_state',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $gst2->id,
            'name' => $productModels[4]->name,
            'hsn_sac_code' => $productModels[4]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 35000,
            'gst_rate' => 28,
            'taxable_amount' => 35000,
            'igst_amount' => 9800,
            'line_total' => 44800,
        ]);

        // --- GST Invoice 3 - Overdue ---
        $gst3 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[2]->id,
            'invoice_number' => 'INV-2026-00003',
            'invoice_type' => 'gst_invoice',
            'invoice_date' => '2026-05-01',
            'due_date' => '2026-05-15',
            'subtotal' => 12000,
            'discount_amount' => 0,
            'taxable_amount' => 12000,
            'total_gst_amount' => 2160,
            'cgst_amount' => 1080,
            'sgst_amount' => 1080,
            'igst_amount' => 0,
            'grand_total' => 14160,
            'paid_amount' => 0,
            'balance_due' => 14160,
            'status' => 'overdue',
            'place_of_supply' => 'intra_state',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $gst3->id,
            'name' => $productModels[5]->name,
            'hsn_sac_code' => $productModels[5]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 12000,
            'gst_rate' => 18,
            'taxable_amount' => 12000,
            'cgst_amount' => 1080,
            'sgst_amount' => 1080,
            'line_total' => 14160,
        ]);

        // --- GST Invoice 4 - Export (0% GST) ---
        $gst4 = Invoice::create([
            'company_id' => $company1->id,
            'client_id' => $clientModels[4]->id,
            'invoice_number' => 'INV-2026-00004',
            'invoice_type' => 'gst_invoice',
            'invoice_date' => '2026-06-15',
            'due_date' => '2026-07-15',
            'subtotal' => 50000,
            'discount_amount' => 0,
            'taxable_amount' => 50000,
            'total_gst_amount' => 0,
            'igst_amount' => 0,
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'grand_total' => 50000,
            'paid_amount' => 0,
            'balance_due' => 50000,
            'status' => 'sent',
            'place_of_supply' => 'export',
            'gst_mode' => 'exclusive',
            'created_by' => $admin1->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $gst4->id,
            'name' => $productModels[0]->name,
            'hsn_sac_code' => $productModels[0]->hsn_sac_code,
            'quantity' => 1,
            'unit_price' => 50000,
            'gst_rate' => 0,
            'taxable_amount' => 50000,
            'igst_amount' => 0,
            'line_total' => 50000,
        ]);

        echo "\n✅ Sample data seeded!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Companies: 3\n";
        echo "Users: 6\n";
        echo "Clients: 5 (Demo Business)\n";
        echo "Products: 9 (6 Services + 3 Fabrics)\n";
        echo "Invoices: 6 (2 Proforma + 4 GST)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Login:\n";
        echo "  Owner: admin@example.com / password\n";
        echo "  Staff: priya@demobusiness.com / password\n";
    }
}
