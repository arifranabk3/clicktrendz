<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class WhatsappConfig extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'phone_number',
        'label',
        'is_active',
        'is_hidden',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
    ];
}
