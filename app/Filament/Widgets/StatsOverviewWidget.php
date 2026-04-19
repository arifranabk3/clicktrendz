<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Country;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class StatsOverviewWidget extends BaseWidget
{
    #[On('refreshDashboard')]
    public function refresh(): void {}

    public ?string $country_id = null;
    public ?string $label_prefix = null;

    protected function getStats(): array
    {
        $countryId = session('current_country_id', 'all');
        $isGlobal = ($countryId === 'all');
        
        $country = $isGlobal ? null : Country::find($countryId);
        $prefixLabel = $isGlobal ? 'Empire' : ($country?->name ?? 'Local');
        
        // Dynamic Flag Injection for Clinical UX
        $flagHtml = $country ? "<img src='/assets/flags/{$country->code}.svg' class='inline-block size-6 mr-2' /> " : "";
        
        $currency = $isGlobal ? 'PKR' : ($country?->currency_code ?? 'PKR');

        $data = Cache::remember("stats:v3:" . ($countryId ?? 'global'), 300, function () {
            // No manual filtering here! ScopedByCountry trait handles it automagically.
            $query = Order::query()->whereDate('created_at', today());

            return [
                'revenue' => (float) $query->sum('total_amount'),
                'profit' => (float) $query->sum('margin_amount'),
                'orderCount' => (int) $query->count(),
                // Unscoped Global Metric: Inventory Health
                'global_inventory' => (int) \App\Models\Product::withoutGlobalScopes()->sum('stock_quantity'),
            ];
        });

        $revenue = $data['revenue'];
        $profit = $data['profit'];
        $orderCount = $data['orderCount'];
        $globalInventory = $data['global_inventory'];

        $aov = ($orderCount > 0) ? ($revenue / $orderCount) : 0;
        $fuel = \App\Services\ScalingService::calculateMarketingFuel($countryId);

        return [
            Stat::make($prefixLabel . ' Revenue', number_format($revenue, 2) . ' ' . $currency)
                ->label($flagHtml . $prefixLabel . ' Revenue')
                ->description($isGlobal ? 'Aggregate Empire revenue today' : 'Market-specific sales today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make($prefixLabel . ' Net Profit', number_format($profit, 2) . ' ' . $currency)
                ->description('Estimated post-sourcing margin')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Global Inventory', number_format($globalInventory))
                ->description('Total Stock across all regions')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),

            Stat::make($prefixLabel . ' Marketing Fuel', number_format($fuel['budget'], 2) . ' ' . $currency)
                ->description('20% of Net Profit for Reinvestment')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning'),
        ];
    }
}
