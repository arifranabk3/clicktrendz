<?php

namespace App\Services\Wholesalers;

class MarkazService extends AbstractWholesalerService
{
    public function fetchProducts(array $params = []): array
    {
        // Mocking Markaz API structure
        // In real world: $this->getHttpClient()->get('/products', $params);
        return [
            [
                'external_id' => 'mkZ_123',
                'name' => 'Premium Cotton Bedding',
                'price' => 2500,
                'cost' => 1500,
                'image' => 'https://via.placeholder.com/400x500',
            ]
        ];
    }

    public function syncProduct(string $externalId): bool
    {
        return true;
    }
}
