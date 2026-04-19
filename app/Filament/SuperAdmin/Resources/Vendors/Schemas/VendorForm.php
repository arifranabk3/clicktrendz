<?php

namespace App\Filament\SuperAdmin\Resources\Vendors\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('business_id')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Toggle::make('is_active')
                    ->label('Active Status')
                    ->default(true)
                    ->required(),
                KeyValue::make('metadata')
                    ->label('Strategic Attributes')
                    ->addActionLabel('Add Attribute')
                    ->reorderable(),
            ]);
    }
}
