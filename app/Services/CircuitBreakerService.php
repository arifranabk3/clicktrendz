<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class CircuitBreakerService
{
    private const THRESHOLD = 5; // Failures before opening
    private const TIMEOUT = 60;   // Seconds to stay open

    /**
     * Strategic Resilience: Execute vendor calls with Circuit Breaker protection.
     */
    public function execute(string $service, callable $callback)
    {
        if ($this->isOpen($service)) {
            Log::warning("Circuit Breaker: '{$service}' is currently OPEN. Aborting request to prevent system hanging.");
            throw new \Exception("Service '{$service}' is temporarily unavailable.");
        }

        try {
            $result = $callback();
            $this->reset($service);
            return $result;
        } catch (\Exception $e) {
            $this->trackFailure($service);
            Log::error("Circuit Breaker: Error in '{$service}'. Failure tracked. Error: {$e->getMessage()}");
            throw $e;
        }
    }

    private function isOpen(string $service): bool
    {
        return (bool) Redis::get("cb:open:{$service}");
    }

    private function trackFailure(string $service): void
    {
        $failures = Redis::incr("cb:failures:{$service}");
        
        if ($failures >= self::THRESHOLD) {
            Redis::setex("cb:open:{$service}", self::TIMEOUT, 1);
            Log::emergency("Circuit Breaker: '{$service}' THRESHOLD REACHED. Circuit is now OPEN for " . self::TIMEOUT . "s.");
        }
    }

    private function reset(string $service): void
    {
        Redis::del("cb:failures:{$service}");
    }
}
