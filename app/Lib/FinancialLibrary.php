<?php

declare(strict_types=1);

namespace App\Lib;

use App\Models\Order;

class FinancialLibrary
{
    /**
     * Calculate financial data for an order.
     */
    public static function processOrderFinances(Order $order): void
    {
        $sellingPrice = (float) $order->total_amount;
        $sourcingPrice = (float) $order->sourcing_price;
        
        // Margin calculation
        $order->margin_amount = $sellingPrice - $sourcingPrice;

        // Payment Flow Logic
        if ($order->payment_method === 'cod') {
            // For COD, the wholesaler/courier collects the money usually.
            // Receivable = Total - Sourcing - Wholesaler Fee (if any)
            $order->metadata = array_merge($order->metadata ?? [], [
                'receivable_from_wholesaler' => $sellingPrice - $sourcingPrice,
                'payable_to_wholesaler' => 0,
            ]);
        } else {
            // For Card payments, platform collects the money.
            // Payable = Sourcing cost
            $order->metadata = array_merge($order->metadata ?? [], [
                'payable_to_wholesaler' => $sourcingPrice,
                'receivable_from_wholesaler' => 0,
            ]);
        }

        $order->save();
    }
}
