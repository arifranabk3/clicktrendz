<?php

namespace App\Services\Wholesalers;

use App\Models\Wholesaler;

interface WholesalerServiceInterface
{
    /**
     * Set the wholesaler context.
     */
    public function setWholesaler(Wholesaler $wholesaler): self;

    /**
     * Fetch products from the wholesaler API.
     */
    public function fetchProducts(array $params = []): array;

    /**
     * Sync a specific product's stock and price.
     */
    public function syncProduct(string $externalId): bool;
}
