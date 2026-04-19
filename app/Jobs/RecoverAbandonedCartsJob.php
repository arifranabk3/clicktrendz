<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AbandonedCart;
use App\Models\AgentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecoverAbandonedCartsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     * Autonomous recovery of revenue.
     */
    public function handle(): void
    {
        Log::info("Running Abandoned Cart Recovery Agent...");

        $threshold = now()->subMinutes(15);

        $carts = AbandonedCart::where('last_activity_at', '<=', $threshold)
            ->where('is_notified', false)
            ->get();

        foreach ($carts as $cart) {
            $this->triggerWhatsappRecovery($cart);
        }
    }

    private function triggerWhatsappRecovery(AbandonedCart $cart): void
    {
        Log::info("Triggering Abandoned Cart recovery via WhatsApp for Session #{$cart->session_id}");

        // In real logic: WhatsappService::send($cart->customer_phone, "You left something in your cart!");
        
        AgentLog::create([
            'agent_type' => 'whatsapp_recovery_bot',
            'action' => 'abandoned_cart_notified',
            'details' => json_encode([
                'session_id' => $cart->session_id,
                'phone' => $cart->customer_phone,
                'amount' => $cart->total_amount,
            ]),
        ]);

        $cart->update(['is_notified' => true]);
    }
}
