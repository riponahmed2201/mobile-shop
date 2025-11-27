<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenancy\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::create([
            'tenant_code' => 'SHOP001',
            'shop_name' => 'Mobile Shop Demo',
            'owner_name' => 'Admin User',
            'owner_email' => 'admin@mobileshop.com',
            'owner_phone' => '+8801712345678',
            'business_license' => 'BL-2024-001',
            'shop_address' => '123 Main Street',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'country' => 'Bangladesh',
            'postal_code' => '1000',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'subscription_status' => 'ACTIVE',
            'subscription_start_date' => now(),
            'subscription_end_date' => now()->addYear(),
            'is_active' => true,
        ]);
    }
}
