<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Models\AgentLog;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AgentLogResource extends Resource
{
    public static function getModel(): string
    {
        return AgentLog::class;
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cpu-chip';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('activity_type')->readOnly(),
                \Filament\Forms\Components\Textarea::make('message')->readOnly(),
                \Filament\Forms\Components\Textarea::make('ai_thought')->readOnly(),
                \Filament\Forms\Components\KeyValue::make('metadata')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('activity_type')->searchable(),
                Tables\Columns\TextColumn::make('message')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => AgentLogResource\Pages\ListAgentLogs::route('/'),
        ];
    }
}
