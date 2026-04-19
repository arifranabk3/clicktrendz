<?php

declare(strict_types=1);

namespace App\Services\Wholesaler;

use App\Models\Wholesaler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarkazAdapter implements WholesalerAdapterInterface
{
    public function fetchProducts(Wholesaler $wholesaler): array
    {
        Log::info("Fetching products from Markaz API for {$wholesaler->name}");

        // In production: $response = Http::withToken($wholesaler->api_key)->get($wholesaler->website_url . '/api/v1/products');
        
        // Mocking Standardized Output for Phase 5 proof-of-concept
        return [
            [
                'sku' => 'MKZ-PK-001',
                'title' => 'Markaz Trendz Watch',
                'description' => 'A premium trending watch for the Pakistan market.',
                'sourcing_price' => 1200.00,
                'selling_price' => 2500.00,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=Markaz+Watch',
                'stock_count' => 150,
            ],
            [
                'sku' => 'MKZ-PK-002',
                'title' => 'Markaz Air Buds',
                'description' => 'High quality acoustic buds.',
                'sourcing_price' => 800.00,
                'selling_price' => 1800.00,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=Markaz+Buds',
                'stock_count' => 50,
            ]
        ];
    }

    public function pushOrder(Wholesaler $wholesaler, array $orderData): bool
    {
        Log::info("Pushing order to Markaz API...");
        return true;
    }
}
