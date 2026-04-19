<?php

namespace App\Filament\SuperAdmin\Resources\WhatsappConfigResource\Pages;

use App\Filament\SuperAdmin\Resources\WhatsappConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsappConfig extends EditRecord
{
    protected static string $resource = WhatsappConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
