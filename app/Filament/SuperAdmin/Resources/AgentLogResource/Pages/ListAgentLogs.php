<?php

namespace App\Filament\SuperAdmin\Resources\AgentLogResource\Pages;

use App\Filament\SuperAdmin\Resources\AgentLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentLogs extends ListRecords
{
    protected static string $resource = AgentLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
