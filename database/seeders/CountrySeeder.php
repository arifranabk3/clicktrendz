<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Pakistan',
                'code' => 'PK',
                'currency_code' => 'PKR',
                'currency_symbol' => 'Rs.',
                'locale' => 'ur',
            ],
            [
                'name' => 'United Arab Emirates',
                'code' => 'UAE',
                'currency_code' => 'AED',
                'currency_symbol' => 'د.إ',
                'locale' => 'ar',
            ],
            [
                'name' => 'Saudi Arabia',
                'code' => 'KSA',
                'currency_code' => 'SAR',
                'currency_symbol' => 'ر.س',
                'locale' => 'ar',
            ],
            [
                'name' => 'Qatar',
                'code' => 'QA',
                'currency_code' => 'QAR',
                'currency_symbol' => 'ر.ق',
                'locale' => 'ar',
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }
    }
}
