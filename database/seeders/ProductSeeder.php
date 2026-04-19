<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'business_id' => 1,
                'name' => 'Premium Leather Wallet',
                'slug' => 'premium-leather-wallet',
                'description' => 'Genuine Cow Leather Wallet with 6 card slots and 1 cash compartment. Hand-stitched for durability.',
                'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?auto=format&fit=crop&q=80&w=800',
                'sku' => 'LW-001',
                'stock_quantity' => 100,
                'price' => 2500.00,
                'supplier_price' => 1200.00,
                'shipping_cost' => 200.00,
            ],
            [
                'business_id' => 1,
                'name' => 'Minimalist Analog Watch',
                'slug' => 'minimalist-analog-watch',
                'description' => 'Sleek black analog watch with a minimalist dial and stainless steel mesh strap.',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=800',
                'sku' => 'AW-002',
                'stock_quantity' => 50,
                'price' => 4500.00,
                'supplier_price' => 2500.00,
                'shipping_cost' => 250.00,
            ],
            [
                'business_id' => 1,
                'name' => 'Ergonomic Desk Lamp',
                'slug' => 'ergonomic-desk-lamp',
                'description' => 'Adjustable LED desk lamp with 3 color modes and 5 brightness levels. USB charging port included.',
                'image' => 'https://images.unsplash.com/photo-1534073828943-f801091e70e?auto=format&fit=crop&q=80&w=800',
                'sku' => 'DL-003',
                'stock_quantity' => 75,
                'price' => 3800.00,
                'supplier_price' => 1800.00,
                'shipping_cost' => 300.00,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
