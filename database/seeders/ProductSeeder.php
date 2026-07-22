<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Company;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        // Common product templates
        $serviceProducts = [
            ['item_no' => 'S001', 'name' => 'Web Development', 'hsn_sac_code' => '998313', 'unit_price' => 50000, 'gst_rate' => 18, 'unit' => 'project'],
            ['item_no' => 'S002', 'name' => 'IT Consulting', 'hsn_sac_code' => '998314', 'unit_price' => 25000, 'gst_rate' => 18, 'unit' => 'hour'],
        ];

        $fabricProducts = [
            [
                'item_no' => 'F001',
                'name' => 'Cotton White Fabric',
                'hsn_sac_code' => '5208',
                'unit_price' => 120,
                'gst_rate' => 5,
                'unit' => 'Mtr',
                'stock' => 500,
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Meter',
                'consumption_per_piece' => null,
                'minimum_stock' => 50,
            ],
            [
                'item_no' => 'F002',
                'name' => 'Formal Shirt (Piece)',
                'hsn_sac_code' => '6205',
                'unit_price' => 899,
                'gst_rate' => 12,
                'unit' => 'Pcs',
                'stock' => 500,
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Piece',
                'consumption_per_piece' => 1.60,
                'minimum_stock' => 20,
            ],
            [
                'item_no' => 'F003',
                'name' => 'Kurta (Piece)',
                'hsn_sac_code' => '6211',
                'unit_price' => 1299,
                'gst_rate' => 5,
                'unit' => 'Pcs',
                'stock' => 500,
                'stock_unit' => 'Mtr',
                'stock_deduction_type' => 'Piece',
                'consumption_per_piece' => 2.40,
                'minimum_stock' => 15,
            ],
        ];

        foreach ($companies as $company) {
            // Service products
            foreach ($serviceProducts as $prod) {
                Product::create(array_merge($prod, [
                    'company_id' => $company->id,
                    'stock' => 0,
                    'stock_unit' => 'Mtr',
                    'stock_deduction_type' => 'Meter',
                    'selling_price' => $prod['unit_price'],
                ]));
            }
            // Fabric products
            foreach ($fabricProducts as $prod) {
                Product::create(array_merge($prod, [
                    'company_id' => $company->id,
                    'selling_price' => $prod['unit_price'],
                ]));
            }
        }
    }
}
