<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Company;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $allStates = [
            ['name' => 'Maharashtra', 'code' => '27'],
            ['name' => 'Karnataka',   'code' => '29'],
            ['name' => 'Gujarat',     'code' => '24'],
            ['name' => 'Delhi',       'code' => '07'],
            ['name' => 'Tamil Nadu',  'code' => '33'],
        ];

        foreach ($companies as $company) {
            $companyStateCode = $company->state_code;
            $companyStateName = $company->state;

            // Intra‑state client
            Client::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'gstin'      => $companyStateCode . 'AAAAA0000A1Z5',  // unique key
                ],
                [
                    'client_type'  => 'business',
                    'name'         => 'Local Client ' . $company->name,
                    'company_name' => 'Local Corp',
                    'email'        => 'local@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone'        => '022-12345678',
                    'state_code'   => $companyStateCode,
                    'state_name'   => $companyStateName,
                    'state'        => $companyStateName,
                    'address_line_1' => 'Local Address',
                    'city'         => $company->city,
                    'pincode'      => '400001',
                    'place_of_supply' => 'intra_state',
                    'status'       => 'active',
                ]
            );

            // Inter‑state client (different state)
            $otherStates = array_filter($allStates, fn($s) => $s['code'] !== $companyStateCode);
            if (empty($otherStates)) {
                $otherStates = [['name' => 'Delhi', 'code' => '07']];
            }
            $otherState = $otherStates[array_rand($otherStates)];

            Client::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'gstin'      => $otherState['code'] . 'BBBBB0000A1Z6',
                ],
                [
                    'client_type'  => 'business',
                    'name'         => 'Outstation Client ' . $company->name,
                    'company_name' => 'Outstation Corp',
                    'email'        => 'out@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone'        => '080-87654321',
                    'state_code'   => $otherState['code'],
                    'state_name'   => $otherState['name'],
                    'state'        => $otherState['name'],
                    'address_line_1' => 'Outstation Address',
                    'city'         => 'Bangalore',
                    'pincode'      => '560100',
                    'place_of_supply' => 'inter_state',
                    'status'       => 'active',
                ]
            );

            // Individual client (no GSTIN – use a unique email instead)
            Client::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'email'      => 'individual@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                ],
                [
                    'client_type'  => 'individual',
                    'name'         => 'Individual Customer',
                    'phone'        => '9876543210',
                    'state_code'   => $companyStateCode,
                    'state_name'   => $companyStateName,
                    'state'        => $companyStateName,
                    'address_line_1' => '42, Palm Street',
                    'city'         => $company->city,
                    'pincode'      => '400050',
                    'place_of_supply' => 'intra_state',
                    'status'       => 'active',
                ]
            );
        }
    }
}
