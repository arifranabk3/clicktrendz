<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\AgentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TriggerWhatsappRecoveryJob implements ShouldQueue
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
        Log::info("Triggering WhatsApp recovery for Order #{$this->order->id}. Reason: Incomplete Address.");

        // Stub logic for WhatsApp Bot Trigger
        // In real world: Http::post('whatsapp-gateway', [...]);
        
        AgentLog::create([
            'business_id' => $this->order->business_id,
            'agent_type' => 'whatsapp_bot',
            'action' => 'address_recovery_triggered',
            'details' => json_encode([
                'order_id' => $this->order->id,
                'phone' => $this->order->customer_phone,
                'message' => 'Please provide a complete address for your order.',
            ]),
        ]);
    }
}
