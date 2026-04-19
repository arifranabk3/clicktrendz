<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Country;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class LifetimeStatsWidget extends BaseWidget
{
    #[On('refreshDashboard')]
    public function refresh(): void {}

    protected ?string $pollingInterval = '60s';

    public ?string $country_id = null;

    protected function getStats(): array
    {
        $effectiveCountryId = $this->country_id ?? session('current_country', 'all');
        $isGlobal = ($effectiveCountryId === 'all');
        $labelSuffix = $isGlobal ? " (Global - All Countries)" : " (" . (Country::find($effectiveCountryId)?->code ?? 'Local') . ")";
        
        $cacheKey = "stats:lifetime:" . ($effectiveCountryId ?? 'global');

        $data = Cache::remember($cacheKey, 3600, function () {
            $query = Order::query();

            if ($this->country_id && $this->country_id !== 'all') {
                $query->where('country_id', $this->country_id);
            }

            return [
                'lifetimeGmv' => (float) (clone $query)->sum('total_amount'),
                'lifetimeOrders' => (int) (clone $query)->count(),
                'weeklyGmv' => (float) (clone $query)
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->sum('total_amount'),
            ];
        });

        $currency = 'PKR';
        if ($this->country_id && $this->country_id !== 'all') {
            $country = Country::find($this->country_id);
            $currency = $country ? $country->currency_code : 'PKR';
        }

        return [
            Stat::make('Weekly Revenue' . $labelSuffix, number_format($data['weeklyGmv'], 2) . ' ' . $currency)
                ->description($isGlobal ? 'Aggregate revenue this week' : 'Sales for the current week')
                ->color('info'),

            Stat::make('Lifetime GMV' . $labelSuffix, number_format($data['lifetimeGmv'], 2) . ' ' . $currency)
                ->description($isGlobal ? 'Total cross-border market value' : 'Total Gross Merchandise Value')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Lifetime Orders' . $labelSuffix, $data['lifetimeOrders'])
                ->description($isGlobal ? 'Total volume since founding' : 'Aggregate volume since launch')
                ->color('gray'),
        ];
    }
}
