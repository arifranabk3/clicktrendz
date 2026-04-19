<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\MarketingLedger;
use App\Services\ScalingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CalculateDailyBudgetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     * Scheduled at 12:00 AM daily to freeze financial snapshots.
     */
    public function handle(): void
    {
        Log::info("Freezing Daily Financial Ledger and Calculating Marketing Fuel...");

        $yesterday = now()->subDay()->toDateString();
        
        // Calculate Global Status for Yesterday
        $fuelStats = ScalingService::calculateMarketingFuel(); // Modify this to accept a date if needed, but for midnight run, 'today' is yesterday's date if run exactly at 00:00 or subDay() logic in Query.
        
        // Accurate Yesterday Stats
        $query = Order::whereDate('created_at', $yesterday);
        $revenue = (float) $query->sum('total_amount');
        $profit = (float) $query->sum('margin_amount');

        $tier = ($revenue >= 50000) ? 2 : 1;
        $percentage = ($tier === 2) ? 0.08 : 0.05;
        $budgetAllocated = $profit * $percentage;

        MarketingLedger::updateOrCreate(
            ['date' => $yesterday],
            [
                'total_revenue' => $revenue,
                'net_profit' => $profit,
                'budget_allocated' => $budgetAllocated,
                'tier' => $tier,
            ]
        );

        // Pre-cache the new day's starting budget
        Cache::forget("stats:today:global");
        Log::info("Ledger created for {$yesterday}. Budget allocated: {$budgetAllocated}");
    }
}
