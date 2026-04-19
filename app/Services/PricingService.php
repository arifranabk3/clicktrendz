<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use Exception;

class PricingService
{
    /**
     * Strategic Pricing Engine (Phase 2)
     * 
     * Formula: (Base + Shipping) + (25% Aggressive Margin) + (Regional Tax) = Selling Price.
     * 
     * @param float $basePrice Original sourcing cost of the item.
     * @param float $shipping Regional shipping/logistics cost.
     * @param string $countryCode ISO code for regional tax mapping (PK, AE, SA, QA).
     * @return array Calculated pricing breakdown.
     */
    public function calculateFinalPrice(float $basePrice, float $shipping, string $countryCode): array
    {
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            throw new Exception("Strategic Error: Country configuration missing for code '{$countryCode}'. Pricing cannot be determined.");
        }

        $costSubtotal = $basePrice + $shipping;
        
        // Aggressive 25% Markup Rule
        $margin = $costSubtotal * 0.25;

        // Regional Tax Logic (GST/VAT)
        $taxPercentage = (float) ($country->tax_percentage ?? 0);
        $taxAmount = $costSubtotal * ($taxPercentage / 100);

        $sellingPrice = $costSubtotal + $margin + $taxAmount;

        // Safety Lock: Verify that net margin survives after tax and processing fees (estimated)
        $netProfit = $sellingPrice - ($costSubtotal + $taxAmount);
        $marginPercentage = ($sellingPrice > 0) ? ($netProfit / $sellingPrice) * 100 : 0;

        return [
            'base_price' => $basePrice,
            'shipping' => $shipping,
            'cost_subtotal' => $costSubtotal,
            'margin_amount' => $margin,
            'tax_amount' => $taxAmount,
            'tax_label' => $country->tax_label,
            'selling_price' => round($sellingPrice, 2),
            'net_profit' => round($netProfit, 2),
            'margin_percentage' => round($marginPercentage, 2),
            'currency' => $country->currency_code,
            'is_compliant' => ($marginPercentage >= 20.0), // Safety check
        ];
    }
}
