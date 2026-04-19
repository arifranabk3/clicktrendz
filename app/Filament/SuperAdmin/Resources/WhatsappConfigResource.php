<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Models\WhatsappConfig;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class WhatsappConfigResource extends Resource
{
    public static function getModel(): string
    {
        return WhatsappConfig::class;
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('phone_number')->required(),
                \Filament\Forms\Components\TextInput::make('label')->required(),
                \Filament\Forms\Components\Toggle::make('is_active')->default(true),
                \Filament\Forms\Components\Toggle::make('is_hidden')->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')->searchable(),
                Tables\Columns\TextColumn::make('label')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_hidden')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => WhatsappConfigResource\Pages\ListWhatsappConfigs::route('/'),
            'create' => WhatsappConfigResource\Pages\CreateWhatsappConfig::route('/create'),
            'edit' => WhatsappConfigResource\Pages\EditWhatsappConfig::route('/{record}/edit'),
        ];
    }
}
