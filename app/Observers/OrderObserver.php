<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Order;
use App\Jobs\TriggerWhatsappRecoveryJob;
use App\Jobs\PushOrderToWholesalerJob;

class OrderObserver
{
    /**
     * Handle the Order "creating" event.
     * ClickTrendz Gatekeeper logic for Address Integrity.
     */
    public function creating(Order $order): void
    {
        // Default verification status
        $order->is_verified = true;

        // Auto-Recovery trigger: Address < 20 characters is suspicious for E-com delivery
        if (strlen($order->shipping_address) < 20) {
            $order->is_verified = false;
            $order->status = 'pending_recovery';
        }

        // Financial Ledgering: Calculate margin on create
        // Capture margin (Selling Price - Sourcing Price) for the Daily Ledger
        if ($order->selling_price > 0 && $order->sourcing_price > 0) {
            $order->margin_amount = $order->selling_price - $order->sourcing_price;
        }

        // For multi-item orders, total_amount should already be passed or calculated
        if (empty($order->total_amount)) {
            $order->total_amount = $order->selling_price;
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Hybrid Sync: Inform frontend of new order
        app(\App\Services\WebhookService::class)->dispatch('order.created', [
            'order_id' => $order->id,
            'status' => $order->status,
            'amount' => $order->total_amount,
            'timestamp' => now()->toIso8601String(),
        ]);

        if (!$order->is_verified) {
            TriggerWhatsappRecoveryJob::dispatch($order);
        } else {
            // If verified on birth, push to wholesaler instantly
            PushOrderToWholesalerJob::dispatch($order);
        }
    }

    /**
     * Handle the Order "updated" event.
     * Auto-Dispatch logic for straight-through processing.
     */
    public function updated(Order $order): void
    {
        // Hybrid Sync: Inform frontend of state change
        if ($order->wasChanged('status') || $order->wasChanged('courier_status')) {
            app(\App\Services\WebhookService::class)->dispatch('order.updated', [
                'order_id' => $order->id,
                'status' => $order->status,
                'courier_status' => $order->courier_status,
                'tracking_id' => $order->tracking_id,
                'updated_at' => now()->toIso8601String(),
            ]);
        }

        // If order was just verified by an agent/bot, push to wholesaler
        if ($order->wasChanged('is_verified') && $order->is_verified) {
            PushOrderToWholesalerJob::dispatch($order);
        }
    }
}
