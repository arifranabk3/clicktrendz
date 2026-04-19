<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class ScalingService
{
    /**
     * Calculate current marketing budget derived from Net Profit.
     * 
     * Rule: 
     * - Tier 1: 5% of Net Profit.
     * - Tier 2: 8% if Daily Revenue >= 50,000.
     */
    public static function calculateMarketingFuel(string $countryId = null): array
    {
        $todayQuery = Order::whereDate('created_at', today());
        
        if ($countryId) {
            $todayQuery->where('country_id', $countryId);
        }

        $dailyRevenue = (float) $todayQuery->sum('total_amount');
        $dailyProfit = (float) $todayQuery->sum('margin_amount');

        // Strategic Phase 7 Mandate: Flat 20% Reinvestment of Net Profit
        $percentage = 0.20;
        $budget = $dailyProfit * $percentage;

        return [
            'revenue' => $dailyRevenue,
            'net_profit' => $dailyProfit,
            'percentage' => $percentage * 100,
            'budget' => $budget,
        ];
    }

    /**
     * Get the global scaling status.
     */
    public static function getGlobalFuelStatus(): array
    {
        return self::calculateMarketingFuel();
    }
}
