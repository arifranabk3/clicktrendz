<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;

class Settings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    protected static string | UnitEnum | null $navigationGroup = 'Infrastructure';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Gatekeeper Controls')
                    ->description('Master switches for ClickTrendz storefront visibility.')
                    ->schema([
                        Toggle::make('site_visibility')
                            ->label('Storefront Active')
                            ->helperText('Toggling this off will hide the shop from public access.'),
                        Toggle::make('maintenance_mode')
                            ->label('Maintenance Mode')
                            ->helperText('Enables a branded splash screen for all users.'),
                    ])->columns(2),

                Section::make('Advertising Pixels')
                    ->description('Configure global tracking pixels for marketing attribution.')
                    ->schema([
                        TextInput::make('facebook_pixel_id')
                            ->label('Facebook Pixel ID'),
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID'),
                        TextInput::make('tiktok_pixel_id')
                            ->label('TikTok Pixel ID'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->action(function () {
                    foreach ($this->data as $key => $value) {
                        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                    }

                    Notification::make()
                        ->title('Settings Saved')
                        ->success()
                        ->send();
                })
        ];
    }
}
