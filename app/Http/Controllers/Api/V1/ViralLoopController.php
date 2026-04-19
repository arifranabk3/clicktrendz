<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ViralLoopController extends Controller
{
    /**
     * Track a WhatsApp share event.
     * 
     * Rule: Unlock reward after 3 unique shares (tracked by session/token).
     */
    public function trackShare(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->ip();
        $cacheKey = "viral_shares_{$sessionId}";

        $shares = (int) Cache::get($cacheKey, 0);
        $shares++;

        Cache::put($cacheKey, $shares, now()->addHours(24));

        return response()->json([
            'success' => true,
            'shares' => $shares,
            'remaining' => max(0, 3 - $shares),
            'unlocked' => $shares >= 3,
            'reward_token' => ($shares >= 3) ? encrypt(['type' => 'viral_reward', 'expires' => now()->addHour()]) : null
        ]);
    }

    /**
     * Check current share status.
     */
    public function getStatus(Request $request)
    {
        $sessionId = $request->header('X-Session-ID') ?? $request->ip();
        $shares = (int) Cache::get("viral_shares_{$sessionId}", 0);

        return response()->json([
            'shares' => $shares,
            'unlocked' => $shares >= 3,
        ]);
    }
}
