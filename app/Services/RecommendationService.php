<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class RecommendationService
{
    /**
     * Strategic Upsell Logic (Phase 5)
     * 
     * Prioritizes manually defined 'upsell_skus' from JSON metadata,
     * falling back to high-margin items in the same category and country.
     */
    public function getRecommendations(Product $product, int $limit = 4): Collection
    {
        $metadata = $product->metadata ?? [];
        $upsellSkus = $metadata['upsell_skus'] ?? [];

        // Priority 1: Specifically mapped SKUs
        if (!empty($upsellSkus)) {
            $recommended = Product::whereIn('sku', $upsellSkus)
                ->where('is_active', true)
                ->where('country_id', $product->country_id)
                ->get();
            
            if ($recommended->count() >= $limit) {
                return $recommended->take($limit);
            }
        }

        // Priority 2: Same country, different products, sorted by Net Profit (Margin Protection)
        $categoryProducts = Product::where('country_id', $product->country_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->orderByRaw('(selling_price - sourcing_price) DESC')
            ->limit($limit)
            ->get();

        return $categoryProducts;
    }
}
