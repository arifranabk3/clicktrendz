<?php

namespace App\Models\Concerns;

use App\Models\Business;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBusiness
{
    protected static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope('business', function (Builder $builder) {
            if (auth()->check() && Filament::getTenant()) {
                $builder->where('business_id', Filament::getTenant()->id);
            }
        });

        static::creating(function (Model $model) {
            if (auth()->check() && Filament::getTenant() && ! $model->business_id) {
                $model->business_id = Filament::getTenant()->id;
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
