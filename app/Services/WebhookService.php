<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Strategic Dispatch: Sync state to the Next.js Frontend.
     * 
     * @param string $event The event name (e.g., 'order.created', 'order.updated').
     * @param array $payload The data to send.
     */
    public function dispatch(string $event, array $payload): void
    {
        $webhookUrl = config('services.frontend.webhook_url');

        if (!$webhookUrl) {
            Log::warning("Webhook Logic: No configuration found for 'services.frontend.webhook_url'. Skipping sync for event: {$event}");
            return;
        }

        try {
            Http::withHeaders([
                'X-ClickTrendz-Event' => $event,
                'X-ClickTrendz-Signature' => hash_hmac('sha256', json_encode($payload), config('app.key')),
            ])->timeout(5)->post($webhookUrl, $payload);
            
            Log::info("Webhook Dispatched: {$event} successfully synchronized with Frontend.");
        } catch (\Exception $e) {
            Log::error("Webhook Failure: Could not sync {$event} with Frontend. Error: {$e->getMessage()}");
        }
    }
}
