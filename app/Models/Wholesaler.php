<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wholesaler extends Model
{
    protected $fillable = [
        'name',
        'type',
        'website_url',
        'api_key',
        'api_secret',
        'country_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'api_key' => 'encrypted',
        'api_secret' => 'encrypted',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
