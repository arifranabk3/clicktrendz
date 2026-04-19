<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class HourlySalesChart extends ChartWidget
{
    public function getHeading(): string
    {
        $effectiveCountryId = $this->country_id ?? session('current_country', 'all');
        $labelSuffix = ($effectiveCountryId === 'all') ? " (Global - All Countries)" : " (" . (\App\Models\Country::find($effectiveCountryId)?->code ?? 'Local') . ")";
        
        return 'Hourly Sales Trend' . $labelSuffix;
    }

    protected ?string $pollingInterval = '30s';

    public ?string $country_id = null;

    protected function getData(): array
    {
        $effectiveCountryId = $this->country_id ?? session('current_country', 'all');
        $cacheKey = "stats:hourly:" . ($effectiveCountryId ?? 'global');

        $data = Cache::remember($cacheKey, 600, function () {
            $query = Order::query()
                ->whereDate('created_at', today())
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
                ->groupBy('hour')
                ->orderBy('hour');

            if ($this->country_id && $this->country_id !== 'all') {
                $query->where('country_id', $this->country_id);
            }

            return $query->get()->pluck('count', 'hour')->toArray();
        });

        // Fill in missing hours
        $chartData = [];
        $labels = [];
        for ($i = 0; $i < 24; $i++) {
            $chartData[] = $data[$i] ?? 0;
            $labels[] = str_pad((string)$i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $chartData,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
