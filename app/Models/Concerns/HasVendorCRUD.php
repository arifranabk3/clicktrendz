<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;

trait HasVendorCRUD
{
    /**
     * List all vendors for the current business.
     */
    public function listVendors(): Collection
    {
        return Vendor::all();
    }

    /**
     * Store a new vendor with append-only metadata support.
     */
    public function storeVendor(array $data): Vendor
    {
        $data['metadata'] = [
            'created_at' => now()->toDateTimeString(),
            'initial_payload' => $data,
            'history' => [
                ['action' => 'created', 'timestamp' => now()->toDateTimeString()]
            ]
        ];

        return Vendor::create($data);
    }

    /**
     * Update vendor with append-only metadata history.
     */
    public function updateVendor(Vendor $vendor, array $data): bool
    {
        $metadata = $vendor->metadata ?? [];
        $metadata['history'][] = [
            'action' => 'updated',
            'timestamp' => now()->toDateTimeString(),
            'changes' => array_diff_assoc($data, $vendor->only(keys($data)))
        ];

        $data['metadata'] = $metadata;

        return $vendor->update($data);
    }

    /**
     * Soft delete a vendor.
     */
    public function deleteVendor(Vendor $vendor): bool
    {
        return $vendor->delete();
    }
}
