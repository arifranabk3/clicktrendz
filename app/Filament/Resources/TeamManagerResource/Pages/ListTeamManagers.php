<?php

namespace App\Filament\Resources\TeamManagerResource\Pages;

use App\Filament\Resources\TeamManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamManagers extends ListRecords
{
    protected static string $resource = TeamManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
