<?php

namespace App\Providers;

use App\Models\Country;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('current_country', function () {
            $countryCode = Request::header('X-Country-Code') ?? Request::get('country_code');
            
            if ($countryCode) {
                return Country::where('code', strtoupper($countryCode))->first();
            }

            return null;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
