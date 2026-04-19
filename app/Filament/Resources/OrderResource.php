<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | UnitEnum | null $navigationGroup = 'E-commerce';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_city')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('shipping_address')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('PKR'),
                    ])->columns(2),

                Section::make('Logistics & Shipping')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'returned' => 'Returned',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('WhatsApp Verified')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_logistics')
                        ->label('Export for Trax/Leopard')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $csv = [];
                            $csv[] = ['Customer Name', 'Address', 'Phone', 'City', 'COD Amount'];

                            foreach ($records as $record) {
                                $csv[] = [
                                    $record->customer_name,
                                    $record->shipping_address,
                                    $record->customer_phone,
                                    $record->shipping_city,
                                    $record->total_amount,
                                ];
                            }

                            $filename = 'logistics_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
                            $path = storage_path('app/public/' . $filename);
                            
                            $handle = fopen($path, 'w');
                            foreach ($csv as $row) {
                                fputcsv($handle, $row);
                            }
                            fclose($handle);

                            return response()->download($path)->deleteFileAfterSend(true);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'create' => OrderResource\Pages\CreateOrder::route('/create'),
            'edit' => OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'customer_name', 'customer_phone'];
    }
}
