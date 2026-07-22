<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (no company)
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'phone' => '9999999999',
        ]);
        $superAdmin->assignRole('super_admin');

        $companies = Company::all();

        foreach ($companies as $company) {
            // Admin / Owner
            $admin = User::create([
                'name' => 'Admin ' . $company->name,
                'email' => 'admin@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                'password' => Hash::make('password'),
                'phone' => '98765' . rand(10000, 99999),
                'company_id' => $company->id,
                'current_company_id' => $company->id,
            ]);
            $admin->assignRole('owner', $company->id);

            // Staff
            $staff = User::create([
                'name' => 'Staff ' . $company->name,
                'email' => 'staff@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                'password' => Hash::make('password'),
                'phone' => '98765' . rand(10000, 99999),
                'company_id' => $company->id,
                'current_company_id' => $company->id,
            ]);
            $staff->assignRole('staff', $company->id);
        }
    }
}
