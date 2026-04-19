<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use App\Models\Concerns\HasCountryIsolation;
use App\Traits\ScopedByCountry;
use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\HasVendorIsolation;

class Order extends Model
{
    use BelongsToBusiness, ScopedByCountry, HasVendorIsolation;

    protected $fillable = [
        'business_id',
        'country_id',
        'vendor_id',
        'status',
        'total_amount',
        'customer_email',
        'metadata',
        'customer_name',
        'customer_phone',
        'shipping_city',
        'shipping_address',
        'tracking_id',
        'courier_status',
        'is_verified',
        'selling_price',
        'sourcing_price',
        'margin_amount',
        'payment_method',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price_at_order')->withTimestamps();
    }

    protected $casts = [
        'metadata' => 'array',
    ];
}
