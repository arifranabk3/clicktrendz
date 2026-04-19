<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasVendorIsolation
{
    /**
     * Boot the trait to apply vendor isolation.
     */
    public static function bootHasVendorIsolation(): void
    {
        if (app()->runningInConsole() || !Auth::check()) {
            return;
        }

        $user = Auth::user();

        // If the user is a vendor staff, restrict to their vendor_id
        if ($user->vendor_id) {
            static::addGlobalScope('vendor_isolation', function (Builder $builder) use ($user) {
                $builder->where('vendor_id', $user->vendor_id);
            });
        }
    }
}
