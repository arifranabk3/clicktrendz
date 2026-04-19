<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait ScopedByCountry
{
    public static function bootScopedByCountry(): void
    {
        static::addGlobalScope('regional', function (Builder $builder) {
            $countryId = session('current_country_id');

            if ($countryId && $countryId !== 'all') {
                $builder->where('country_id', $countryId);
            }
        });

        static::creating(function (Model $model) {
            $countryId = session('current_country_id');
            
            if ($countryId && $countryId !== 'all' && ! $model->country_id) {
                $model->country_id = $countryId;
            }
        });
    }

    /**
     * Scope a query to include all regions.
     */
    public function scopeAllRegions(Builder $query): Builder
    {
        return $query->withoutGlobalScope('regional');
    }
}
