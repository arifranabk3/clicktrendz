<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use App\Models\Concerns\HasCountryIsolation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Concerns\HasVendorIsolation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToBusiness, HasCountryIsolation, HasVendorIsolation, SoftDeletes;

    protected $fillable = [
        'business_id',
        'country_id',
        'wholesaler_id',
        'vendor_id',
        'title',
        'slug',
        'description',
        'image_url',
        'sku',
        'stock_count',
        'selling_price',
        'sourcing_price',
        'shipping_cost',
        'supplier_link',
        'is_active',
        'regional_data',
    ];

    protected $casts = [
        'regional_data' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->title) . '-' . \Illuminate\Support\Str::random(5);
            }
        });
    }

    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(Wholesaler::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price_at_order')->withTimestamps();
    }

    public function getNetProfitAttribute(): float
    {
        return (float) ($this->selling_price - ($this->sourcing_price + ($this->shipping_cost ?? 0)));
    }
}
