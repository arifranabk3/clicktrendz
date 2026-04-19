<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class TestRedisConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Redis connectivity via Facade and Predis client.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Testing Redis Connectivity...");

        try {
            // Test 1: Ping via Facade
            $response = Redis::ping();
            $this->comment("Ping Response: " . (is_string($response) ? $response : json_encode($response)));

            if ($response) {
                $this->info("✅ SUCCESS: Redis is reachable via " . config('database.redis.client') . " client.");
            } else {
                $this->error("❌ FAILURE: Ping received empty response.");
            }

            // Test 2: Set/Get
            Redis::set('clicktrendz_test', 'resilience_verified');
            $val = Redis::get('clicktrendz_test');
            
            if ($val === 'resilience_verified') {
                $this->info("✅ SUCCESS: Set/Get verified.");
            }

        } catch (\Exception $e) {
            $this->error("❌ ERROR: " . $e->getMessage());
            Log::error("Redis Test Failure: " . $e->getMessage());
        }
    }
}
