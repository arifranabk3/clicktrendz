<?php

namespace App\Services\Wholesalers;

use App\Models\Wholesaler;
use Exception;

class WholesalerManager
{
    /**
     * Resolve the correct service for a wholesaler.
     */
    public static function make(Wholesaler $wholesaler): WholesalerServiceInterface
    {
        $serviceClass = match ($wholesaler->type) {
            'markaz' => MarkazService::class,
            'hhc' => HHCService::class, // Need to implement
            'zarya' => ZaryaService::class, // Need to implement
            'cj' => CJService::class, // Need to implement
            default => throw new Exception("Unsupported wholesaler type: {$wholesaler->type}"),
        };

        return app($serviceClass)->setWholesaler($wholesaler);
    }
}
