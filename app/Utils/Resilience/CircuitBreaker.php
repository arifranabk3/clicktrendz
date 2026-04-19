<?php

declare(strict_types=1);

namespace App\Utils\Resilience;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    private const FAILURE_THRESHOLD = 5;
    private const WINDOW_SECONDS = 300; // 5 minutes
    private const OPEN_SECONDS = 600;   // 10 minutes

    /**
     * Check if the circuit for a specific service is open (blocked).
     */
    public static function isOpen(string $service): bool
    {
        return Cache::has("circuit_breaker:open:{$service}");
    }

    /**
     * Record a failure for a specific service.
     */
    public static function recordFailure(string $service): void
    {
        $key = "circuit_breaker:failures:{$service}";
        $failures = (int) Cache::get($key, 0) + 1;

        Cache::put($key, $failures, self::WINDOW_SECONDS);

        if ($failures >= self::FAILURE_THRESHOLD) {
            self::open($service);
        }
    }

    /**
     * Open the circuit (block the service).
     */
    private static function open(string $service): void
    {
        Log::critical("Circuit Breaker OPEN for service: {$service}. Threshold reached.");
        Cache::put("circuit_breaker:open:{$service}", true, self::OPEN_SECONDS);
    }

    /**
     * Reset the failures for a specific service.
     */
    public static function reset(string $service): void
    {
        Cache::forget("circuit_breaker:failures:{$service}");
        Cache::forget("circuit_breaker:open:{$service}");
    }
}
