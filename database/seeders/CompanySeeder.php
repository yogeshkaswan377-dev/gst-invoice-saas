<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Demo Business Pvt Ltd',
                'email' => 'contact@demobusiness.com',
                'phone' => '9876543210',
                'gstin' => '27ABCDE1234F1Z5',
                'pan' => 'ABCDE1234F',
                'state_code' => '27',
                'state' => 'Maharashtra',
                'address_line_1' => '123, Business Park, Andheri East',
                'city' => 'Mumbai',
                'pincode' => '400093',
                'subscription_plan' => 'trial',
                'is_active' => true,
                'gst_settings' => json_encode(['default_rate' => 18, 'default_mode' => 'exclusive']),
                'invoice_preferences' => json_encode(['invoice_prefix' => 'INV', 'proforma_prefix' => 'PRO']),
            ],
            [
                'name' => 'Tech Solutions India',
                'email' => 'info@techsolutions.com',
                'phone' => '9988776655',
                'gstin' => '29AABCT1234E1Z6',
                'state_code' => '29',
                'state' => 'Karnataka',
                'address_line_1' => '456, Tech Park, Whitefield',
                'city' => 'Bangalore',
                'pincode' => '560066',
                'subscription_plan' => 'basic',
                'is_active' => true,
            ],
            [
                'name' => 'Gujarat Textiles',
                'email' => 'info@gujarattextiles.com',
                'phone' => '9876541230',
                'gstin' => '24ABCDE5678F1Z9',
                'state_code' => '24',
                'state' => 'Gujarat',
                'address_line_1' => '789, Textile Market',
                'city' => 'Ahmedabad',
                'pincode' => '380001',
                'subscription_plan' => 'premium',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $data) {
            Company::create($data);
        }
    }
}
