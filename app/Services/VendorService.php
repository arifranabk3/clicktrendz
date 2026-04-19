<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Vendor;
use App\Models\Concerns\HasVendorCRUD;
use Illuminate\Database\Eloquent\Collection;

class VendorService
{
    use HasVendorCRUD;

    /**
     * Get isolated vendors based on current context.
     */
    public function getActiveVendors(): Collection
    {
        return Vendor::where('is_active', true)->get();
    }

    /**
     * Strategic Sync: Placeholder for future vendor-specific data syncing logic.
     */
    public function syncVendorStock(Vendor $vendor): void
    {
        // Future logic for CJ/Markaz/HHC vendor-specific stock syncing
    }
}
