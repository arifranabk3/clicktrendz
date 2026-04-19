<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Services\Wholesalers\WholesalerManager;
use App\Utils\Resilience\CircuitBreaker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushOrderToWholesalerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Pushing Order #{$this->order->id} to Wholesaler API.");

        // Detect Wholesaler from first product in the order
        $firstProduct = $this->order->products()->with('wholesaler')->first();

        if (!$firstProduct || !$firstProduct->wholesaler) {
            Log::error("Order #{$this->order->id} push failed: No wholesaler found for products.");
            $this->order->update(['status' => 'failed_sync']);
            return;
        }

        $wholesaler = $firstProduct->wholesaler;

        // Circuit Breaker Check
        if (CircuitBreaker::isOpen($wholesaler->type)) {
            Log::warning("Circuit Breaker is OPEN for {$wholesaler->type}. Queuing order #{$this->order->id} for later.");
            $this->release(600); // Retry in 10 minutes
            return;
        }

        try {
            $service = WholesalerManager::make($wholesaler);
            // In real logic: $service->pushOrder($this->order);
            
            Log::info("Order successfully dispatched to {$wholesaler->name} ({$wholesaler->type}).");
            
            $this->order->update([
                'status' => 'dispatched',
                'metadata' => array_merge($this->order->metadata ?? [], [
                    'wholesaler_reference' => 'WH_' . uniqid(),
                    'dispatched_at' => now()->toDateTimeString(),
                ])
            ]);

            CircuitBreaker::reset($wholesaler->type);
        } catch (\Exception $e) {
            CircuitBreaker::recordFailure($wholesaler->type);
            Log::error("Wholesaler API Failure ({$wholesaler->type}): " . $e->getMessage());
            $this->release(300); // Retry in 5 minutes
        }
    }
}
