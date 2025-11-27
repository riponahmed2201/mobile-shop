<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'tenant_id' => 1,
                'product_code' => 'SAM-A54-BLK',
                'product_name' => 'Samsung Galaxy A54 5G',
                'brand_id' => 1, // Samsung
                'category_id' => 1, // Smartphones
                'product_type' => 'MOBILE',
                'model_name' => 'Galaxy A54',
                'color' => 'Black',
                'storage' => '128GB',
                'ram' => '8GB',
                'purchase_price' => 35000.00,
                'selling_price' => 42000.00,
                'mrp' => 45000.00,
                'current_stock' => 15,
                'is_active' => true,
            ],
            [
                'tenant_id' => 1,
                'product_code' => 'APP-IP15-BLU',
                'product_name' => 'Apple iPhone 15',
                'brand_id' => 2, // Apple
                'category_id' => 1, // Smartphones
                'product_type' => 'MOBILE',
                'model_name' => 'iPhone 15',
                'color' => 'Blue',
                'storage' => '256GB',
                'ram' => '6GB',
                'purchase_price' => 95000.00,
                'selling_price' => 110000.00,
                'mrp' => 115000.00,
                'current_stock' => 8,
                'is_active' => true,
            ],
            [
                'tenant_id' => 1,
                'product_code' => 'XIA-13P-GRY',
                'product_name' => 'Xiaomi 13 Pro',
                'brand_id' => 3, // Xiaomi
                'category_id' => 1, // Smartphones
                'product_type' => 'MOBILE',
                'model_name' => '13 Pro',
                'color' => 'Gray',
                'storage' => '256GB',
                'ram' => '12GB',
                'purchase_price' => 55000.00,
                'selling_price' => 65000.00,
                'mrp' => 68000.00,
                'current_stock' => 12,
                'is_active' => true,
            ],
            [
                'tenant_id' => 1,
                'product_code' => 'ACC-CASE-001',
                'product_name' => 'Silicone Phone Case',
                'brand_id' => null,
                'category_id' => 3, // Phone Cases
                'product_type' => 'ACCESSORY',
                'color' => 'Transparent',
                'purchase_price' => 150.00,
                'selling_price' => 300.00,
                'mrp' => 350.00,
                'current_stock' => 50,
                'is_active' => true,
            ],
            [
                'tenant_id' => 1,
                'product_code' => 'ACC-CHG-001',
                'product_name' => 'Fast Charger 33W',
                'brand_id' => null,
                'category_id' => 5, // Chargers
                'product_type' => 'ACCESSORY',
                'purchase_price' => 800.00,
                'selling_price' => 1200.00,
                'mrp' => 1500.00,
                'current_stock' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
