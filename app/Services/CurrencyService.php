<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Strategic Normalization: Daily PKR-base Currency Sync.
     * 
     * Formula: All regional revenues are converted to PKR for Empire Reporting.
     */
    public function syncRates(): void
    {
        // Using a generic adapter for exchange rate logic
        foreach (Country::where('is_active', true)->where('code', '!=', 'PK')->get() as $country) {
            $this->updateRateFor($country);
        }
    }

    /**
     * Calculate PKR equivalent for global reporting.
     */
    public function convertToPKR(float $amount, string $fromCurrency): float
    {
        $rate = Cache::get("currency_rate_{$fromCurrency}_to_PKR", 1.0);
        return $amount * (float) $rate;
    }

    private function updateRateFor(Country $country): void
    {
        try {
            // Placeholder for real API call (e.g. ExchangeRate-API)
            // $response = Http::get("https://open.er-api.com/v6/latest/{$country->currency_code}");
            // $rate = $response->json()['rates']['PKR'];
            
            // Hard-coded strategic fallbacks for Phase 6 initial rollout
            $rates = [
                'AED' => 76.50,
                'SAR' => 74.20,
                'QAR' => 76.80,
            ];

            $rate = $rates[$country->currency_code] ?? 1.0;

            Cache::put("currency_rate_{$country->currency_code}_to_PKR", $rate, now()->addDay());
            Log::info("Currency Sync: {$country->currency_code} normalized to {$rate} PKR.");
        } catch (\Exception $e) {
            Log::error("Currency Failure: Could not sync rate for {$country->code}. Error: {$e->getMessage()}");
        }
    }
}
