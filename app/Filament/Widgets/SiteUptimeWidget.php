<?php

namespace App\Filament\Widgets;

use App\Models\AgentLog;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SiteUptimeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $lastError = AgentLog::where('activity_type', 'tool_use')
            ->where('message', 'like', '%scan_logs%')
            ->latest()
            ->first();

        $status = 'Healthy';
        $icon = 'heroicon-m-check-circle';
        $color = 'success';

        if ($lastError && str_contains($lastError->metadata['result'] ?? '', 'ERROR')) {
            $status = 'Errors detected';
            $icon = 'heroicon-m-exclamation-circle';
            $color = 'danger';
        }

        return [
            Stat::make('Site Status', $status)
                ->description('Current site health based on AI logs')
                ->descriptionIcon($icon)
                ->color($color),
        ];
    }
}
