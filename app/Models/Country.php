<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'currency_code',
        'currency_symbol',
        'locale',
        'tax_percentage',
        'tax_label',
        'is_active',
    ];

    protected $casts = [
        'tax_percentage' => 'float',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function wholesalers()
    {
        return $this->hasMany(Wholesaler::class);
    }

    public static function getFlagHtml(?string $code): string
    {
        $code = strtolower($code ?? '');
        
        $mapping = [
            'pk' => 'pk',
            'uae' => 'ae',
            'ae' => 'ae',
            'ksa' => 'sa',
            'sa' => 'sa',
            'qa' => 'qa',
        ];

        $slug = $mapping[$code] ?? null;

        if ($code === 'all' || !$slug) {
            $url = 'https://www.svgrepo.com/show/532363/globe-alt.svg'; // Line-art Global Icon
        } else {
            $url = "https://flagcdn.com/{$slug}.svg";
        }

        return "<img src=\"{$url}\" style=\"width: 50px; height: auto; max-height: 30px; object-fit: contain; border: 1px solid #ddd; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);\" />";
    }
}
