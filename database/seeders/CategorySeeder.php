<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory\ProductCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Mobile Categories
            ['tenant_id' => 1, 'category_name' => 'Smartphones', 'category_type' => 'MOBILE', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Feature Phones', 'category_type' => 'MOBILE', 'parent_category_id' => null, 'is_active' => true],
            
            // Accessory Categories
            ['tenant_id' => 1, 'category_name' => 'Phone Cases', 'category_type' => 'ACCESSORY', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Screen Protectors', 'category_type' => 'ACCESSORY', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Chargers', 'category_type' => 'ACCESSORY', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Earphones', 'category_type' => 'ACCESSORY', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Power Banks', 'category_type' => 'ACCESSORY', 'parent_category_id' => null, 'is_active' => true],
            
            // Parts Categories
            ['tenant_id' => 1, 'category_name' => 'Batteries', 'category_type' => 'PARTS', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Displays', 'category_type' => 'PARTS', 'parent_category_id' => null, 'is_active' => true],
            ['tenant_id' => 1, 'category_name' => 'Motherboards', 'category_type' => 'PARTS', 'parent_category_id' => null, 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
