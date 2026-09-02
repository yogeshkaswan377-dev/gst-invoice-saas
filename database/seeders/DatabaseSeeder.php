<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Company;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    $this->call([
        RoleAndPermissionSeeder::class,
        // UserSeeder will be custom and create only super admin
        UserSeeder::class,
    ]);
}
}
