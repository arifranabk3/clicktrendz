<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wholesaler;
use App\Models\Product;
use App\Services\Wholesalers\WholesalerManager;
use App\Utils\Resilience\CircuitBreaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SyncWholesalerProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Wholesaler $wholesaler)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (CircuitBreaker::isOpen($this->wholesaler->type)) {
            Log::warning("Circuit Breaker is OPEN for {$this->wholesaler->type}. Skipping sync for now.");
            return;
        }

        try {
            $service = WholesalerManager::make($this->wholesaler);
            $externalProducts = $service->fetchProducts();

            foreach ($externalProducts as $item) {
            Product::updateOrCreate(
                [
                    'sku' => $this->wholesaler->type . '_' . $item['external_id'],
                    'country_id' => $this->wholesaler->country_id,
                ],
                [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'supplier_price' => $item['cost'],
                    'shipping_cost' => 250, // Default for now
                    'image' => $item['image'],
                    'wholesaler_id' => $this->wholesaler->id,
                    'is_active' => true, // Observer will hide if margin < 25%
                ]
            );
        }
    } catch (\Exception $e) {
        CircuitBreaker::recordFailure($this->wholesaler->type);
        Log::error("Wholesaler Sync Failure ({$this->wholesaler->type}): " . $e->getMessage());
    }
}
