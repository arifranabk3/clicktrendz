<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class MarketingAsset extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'product_id',
        'ad_copy_hook',
        'ad_copy_body',
        'ad_copy_cta',
        'headline',
        'target_audience',
        'platform',
        'image_prompts',
    ];

    protected $casts = [
        'image_prompts' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
