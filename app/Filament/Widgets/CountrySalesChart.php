<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Country;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CountrySalesChart extends ChartWidget
{
    public ?string $country_id = null;

    public function getHeading(): string
    {
        $country = Country::find($this->country_id);
        return ($country?->name ?? 'Market') . ' Sales Velocity';
    }

    protected function getData(): array
    {
        $cacheKey = "stats:hourly:chart:" . ($this->country_id ?? 'global');

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

        $chartData = [];
        $labels = [];
        for ($i = 0; $i < 24; $i++) {
            $chartData[] = $data[$i] ?? 0;
            $labels[] = str_pad((string)$i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        // Color Mapping logic
        $countryCode = Country::find($this->country_id)?->code;
        $color = match ($countryCode) {
            'PK' => '#10b981', // Green
            'UAE' => '#ef4444', // Red
            'KSA' => '#3b82f6', // Blue
            default => '#3b82f6',
        };

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $chartData,
                    'fill' => 'start',
                    'borderColor' => $color,
                    'backgroundColor' => $color . '1A', // 10% opacity
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
