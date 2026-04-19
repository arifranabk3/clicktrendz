<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     */
    public function saving(Product $product): void
    {
        $this->enforceMarginGuard($product);
    }

    /**
     * Enforce the 25% net margin rule.
     */
    protected function enforceMarginGuard(Product $product): void
    {
        // Avoid calculation if prices are missing
        if (!$product->price || !$product->supplier_price) {
            return;
        }

        $sellingPrice = (float) $product->price;
        $sourcingPrice = (float) $product->supplier_price;
        $shippingCost = (float) ($product->shipping_cost ?? 0);

        if ($sellingPrice <= 0) {
            $product->is_active = false;
            return;
        }

        $netProfit = $sellingPrice - ($sourcingPrice + $shippingCost);
        $margin = ($netProfit / $sellingPrice) * 100;

        // Auto-hide product if margin drops below 25%
        if ($margin < 25) {
            $product->is_active = false;
        } else {
            // If it was hidden but now fixed, we could reactivate,
            // but let's be cautious and only hide automatically.
            // If user manually deactivated, we shouldn't force it active.
        }
    }
}
