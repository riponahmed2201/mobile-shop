<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['tenant_id' => 1, 'brand_name' => 'Samsung', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Apple', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Xiaomi', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Oppo', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Vivo', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Realme', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'OnePlus', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Huawei', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Nokia', 'is_active' => true],
            ['tenant_id' => 1, 'brand_name' => 'Infinix', 'is_active' => true],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
