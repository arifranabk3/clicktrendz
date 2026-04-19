<?php

namespace App\Filament\Resources\CampaignPlannerResource\Pages;

use App\Filament\Resources\CampaignPlannerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignPlanner extends EditRecord
{
    protected static string $resource = CampaignPlannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
