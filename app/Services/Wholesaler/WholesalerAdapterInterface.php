<?php

declare(strict_types=1);

namespace App\Services\Wholesaler;

use App\Models\Wholesaler;

interface WholesalerAdapterInterface
{
    /**
     * Fetch products from the wholesaler API.
     * @return array Standardized product data.
     */
    public function fetchProducts(Wholesaler $wholesaler): array;

    /**
     * Push an order to the wholesaler API.
     * @return bool Success status.
     */
    public function pushOrder(Wholesaler $wholesaler, array $orderData): bool;
}
