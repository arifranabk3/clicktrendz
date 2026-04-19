<x-filament-panels::page>
    {{-- Final Spacing & Gutter Guardrail --}}
    <style>
        /* Force outer layout padding */
        .fi-main {
            padding: 15px !important;
        }
        
        /* Ensure widgets maintain clinical gaps */
        .fi-wi-widget {
            margin-bottom: 15px !important;
        }
        
        /* Reset inner stat margins */
        .fi-wi-stats-overview-stat {
             margin-bottom: 0 !important;
        }

        /* Mobile specific gutter lock */
        @media (max-width: 640px) {
            .fi-main {
                padding: 15px !important;
            }
        }
    </style>

    <div class="p-[15px] space-y-[15px]">
        @php
            $currentCountryId = session('current_country', 'all');
            $activeCountries = \App\Models\Country::where('is_active', true)->get();
            
            if ($currentCountryId !== 'all') {
                $activeCountries = $activeCountries->where('id', $currentCountryId);
            }
        @endphp

        {{-- Top Slot: Empire Global Performance (Always Visible) --}}
        <div class="mb-[15px]">
            <x-filament::section heading="Empire Global Performance" collapsible :collapsed="$currentCountryId !== 'all'">
                <div class="space-y-[15px]">
                    @livewire(\App\Filament\Widgets\StatsOverviewWidget::class, ['country_id' => 'all', 'label_prefix' => 'Empire'])

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-[15px]">
                        @livewire(\App\Filament\Widgets\HourlySalesChart::class, ['country_id' => 'all'])
                        @livewire(\App\Filament\Widgets\LifetimeStatsWidget::class, ['country_id' => 'all'])
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Market Scoped Sections --}}
        @foreach ($activeCountries as $country)
            <div class="mb-[15px]">
                <x-filament::section :heading="new \Illuminate\Support\HtmlString('<div style=\'display: flex; align-items: center; gap: 12px; white-space: nowrap;\'><span style=\'font-weight: 600; font-size: 1.1rem;\'>Market Overview: ' . $country->name . '</span>' . \App\Models\Country::getFlagHtml($country->code) . '</div>')" collapsible>
                    <div class="space-y-[15px]">
                        @livewire(\App\Filament\Widgets\StatsOverviewWidget::class, ['country_id' => (string) $country->id, 'label_prefix' => $country->name])
                        
                        <div class="mt-[15px]">
                            @livewire(\App\Filament\Widgets\CountrySalesChart::class, ['country_id' => (string) $country->id])
                        </div>
                    </div>
                </x-filament::section>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
