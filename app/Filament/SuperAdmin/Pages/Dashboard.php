<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        $panicMode = Setting::where('key', 'is_panic_mode')->first()?->value == '1';

        return [
            Action::make('panic_button')
                ->label($panicMode ? 'DEACTIVATE PANIC MODE' : 'ACTIVATE PANIC BUTTON')
                ->color($panicMode ? 'success' : 'danger')
                ->icon('heroicon-m-exclamation-triangle')
                ->requiresConfirmation()
                ->action(function () use ($panicMode) {
                    Setting::updateOrCreate(['key' => 'is_panic_mode'], ['value' => $panicMode ? '0' : '1']);
                    
                    Notification::make()
                        ->title($panicMode ? 'Panic Mode Deactivated' : 'Panic Mode Activated')
                        ->body($panicMode ? 'AI reporting has been resumed.' : 'All AI reporting has been stopped globally.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
