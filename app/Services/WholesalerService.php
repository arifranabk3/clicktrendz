<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Wholesaler;
use App\Services\Wholesaler\WholesalerAdapterInterface;
use App\Services\Wholesaler\MarkazAdapter;
use App\Services\Wholesaler\HhcAdapter;
use Exception;

class WholesalerService
{
    public static function getAdapter(Wholesaler $wholesaler): WholesalerAdapterInterface
    {
        return match ($wholesaler->type) {
            'markaz' => app(MarkazAdapter::class),
            'hhc' => app(HhcAdapter::class),
            default => throw new Exception("No adapter implemented for Wholesaler type: {$wholesaler->type}"),
        };
    }

    public function syncProducts(Wholesaler $wholesaler): int
    {
        $adapter = self::getAdapter($wholesaler);
        $products = $adapter->fetchProducts($wholesaler);

        $syncedCount = 0;
        foreach ($products as $productData) {
            \App\Models\Product::updateOrCreate(
                [
                    'wholesaler_id' => $wholesaler->id,
                    'sku' => $productData['sku'],
                ],
                [
                    'title' => $productData['title'],
                    'description' => $productData['description'],
                    'sourcing_price' => $productData['sourcing_price'],
                    'selling_price' => $productData['selling_price'],
                    'image_url' => $productData['image_url'],
                    'stock_count' => $productData['stock_count'],
                    'country_id' => $wholesaler->country_id,
                    'is_active' => true,
                ]
            );
            $syncedCount++;
        }

        return $syncedCount;
    }
}
