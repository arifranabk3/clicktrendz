<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use App\Traits\ScopedByCountry;
use Illuminate\Database\Eloquent\Model;

class MarketingStat extends Model
{
    use BelongsToBusiness, ScopedByCountry;

    protected $fillable = [
        'business_id',
        'date',
        'ad_spend',
        'total_sales',
        'roas',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
