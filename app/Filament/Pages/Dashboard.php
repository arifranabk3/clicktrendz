<?php

namespace App\Filament\Pages;

use App\Models\Country;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\HourlySalesChart;
use App\Filament\Widgets\LifetimeStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.dashboard';

    public ?string $country_id = null;
    public ?string $selectedCountry = null;

    public function mount(): void
    {
        $this->country_id = session('current_country', 'all');
        $this->selectedCountry = $this->country_id;
    }

    public function updatedSelectedCountry($value): void
    {
        session(['current_country' => $value]);
        redirect(request()->header('Referer'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filterByCountry')
                ->label('Global Country Filter')
                ->icon('heroicon-m-funnel')
                ->form([
                    Select::make('country_id')
                        ->label('Select Country')
                        ->options(Country::pluck('name', 'id')->prepend('Global (All)', 'all'))
                        ->default(session('current_country', 'all'))
                ])
                ->action(function (array $data) {
                    session(['current_country' => $data['country_id']]);
                    return redirect(request()->header('Referer'));
                })
        ];
    }

    public function getWidgets(): array
    {
        return []; // Rendered manually in Blade for sectioned grouping
    }
}
