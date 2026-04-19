<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Model;

class AgentLog extends Model
{
    use BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'activity_type',
        'message',
        'ai_thought',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
