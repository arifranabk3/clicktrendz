<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SalesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $salesToday = Order::whereDate('created_at', today())->sum('total_amount');
        $ordersToday = Order::whereDate('created_at', today())->count();

        return [
            Stat::make('Total Sales (Today)', '$' . number_format($salesToday, 2))
                ->description('Total revenue generated today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('New Orders', $ordersToday)
                ->description('Orders received today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
