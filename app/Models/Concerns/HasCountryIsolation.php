<?php

namespace App\Models\Concerns;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

trait HasCountryIsolation
{
    public static function bootHasCountryIsolation()
    {
        static::creating(function ($model) {
            $country = app()->has('current_country') ? app('current_country') : null;
            if (empty($model->country_id) && $country && isset($country->id)) {
                $model->country_id = $country->id;
            }
        });

        static::addGlobalScope('country', function (Builder $builder) {
            $country = app()->has('current_country') ? app('current_country') : null;
            if ($country && isset($country->id)) {
                $builder->where('country_id', $country->id);
            }
        });
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
