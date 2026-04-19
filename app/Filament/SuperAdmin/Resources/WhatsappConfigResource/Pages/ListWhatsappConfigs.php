<?php

namespace App\Filament\SuperAdmin\Resources\WhatsappConfigResource\Pages;

use App\Filament\SuperAdmin\Resources\WhatsappConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappConfigs extends ListRecords
{
    protected static string $resource = WhatsappConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
