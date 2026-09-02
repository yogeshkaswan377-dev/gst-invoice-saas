<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'S.M FABEXA',
            'email' => 'smfabexa@gmail.com',   // ← replace with your email
            'password' => Hash::make('password'),
            'phone' => '7227066977',
            'company_id' => null,
            'current_company_id' => null,
        ]);
        $superAdmin->assignRole('super_admin');
    }
}
