<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesalerResource\Pages;
use App\Models\Wholesaler;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;

class WholesalerResource extends Resource
{
    protected static ?string $model = Wholesaler::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | UnitEnum | null $navigationGroup = 'Sourcing';

    public static function isScopedToTenant(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'hhc' => 'HHC',
                                'markaz' => 'Markaz',
                                'zarya' => 'Zarya',
                                'cj' => 'CJ Dropshipping',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('website_url')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('API Credentials')
                    ->schema([
                        Forms\Components\TextInput::make('api_key')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\TextInput::make('api_secret')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),

                Section::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\KeyValue::make('settings')
                            ->helperText('Additional API configurations'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('country.name')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->relationship('country', 'name'),
            ])
            ->actions([
                Action::make('syncProducts')
                    ->label('Sync Products')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Wholesaler $record, \App\Services\WholesalerService $service) {
                        $count = $service->syncProducts($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title("Synced {$count} products.")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWholesalers::route('/'),
            'create' => Pages\CreateWholesaler::route('/create'),
            'edit' => Pages\EditWholesaler::route('/{record}/edit'),
        ];
    }
}
