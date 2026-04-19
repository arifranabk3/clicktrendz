<?php

declare(strict_types=1);

namespace App\Services\Wholesaler;

use App\Models\Wholesaler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HhcAdapter implements WholesalerAdapterInterface
{
    public function fetchProducts(Wholesaler $wholesaler): array
    {
        Log::info("Fetching products from HHC Dropshipping for {$wholesaler->name}");

        // Mocking Standardized Output
        return [
            [
                'sku' => 'HHC-UAE-909',
                'title' => 'HHC Pro Blender',
                'description' => 'Fastest blender for UAE kitchens.',
                'sourcing_price' => 45.00, // AED
                'selling_price' => 120.00,
                'image_url' => 'https://via.placeholder.com/300x300.png?text=HHC+Blender',
                'stock_count' => 300,
            ]
        ];
    }

    public function pushOrder(Wholesaler $wholesaler, array $orderData): bool
    {
        Log::info("Pushing order to HHC API...");
        return true;
    }
}
