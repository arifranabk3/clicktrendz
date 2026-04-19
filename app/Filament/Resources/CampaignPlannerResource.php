<?php

namespace App\Filament\Resources;

use App\Models\MarketingAsset;
use App\Models\Product;
use App\Services\MarketingAIService;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use UnitEnum;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;

class CampaignPlannerResource extends Resource
{
    protected static ?string $model = MarketingAsset::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string | UnitEnum | null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('platform')
                    ->options([
                        'FB' => 'Facebook / Instagram',
                        'TikTok' => 'TikTok',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('target_audience')
                    ->maxLength(255),
                Forms\Components\TextInput::make('headline')
                    ->maxLength(255),
                Section::make('Ad Copies')
                    ->schema([
                        Forms\Components\Textarea::make('ad_copy_hook')
                            ->rows(3),
                        Forms\Components\Textarea::make('ad_copy_body')
                            ->rows(5),
                        Forms\Components\Textarea::make('ad_copy_cta')
                            ->rows(2),
                    ])->columns(1),
                Forms\Components\KeyValue::make('image_prompts')
                    ->label('AI Image Prompts'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'FB' => 'info',
                        'TikTok' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('headline')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                Action::make('generate_ai_copy')
                    ->label('Regenerate AI Copy')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->action(function (MarketingAsset $record) {
                        // This would call MarketingAIService
                        Notification::make()
                            ->title('AI Generation Started')
                            ->body('The agent is crafting high-converting copies...')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => CampaignPlannerResource\Pages\ListCampaignPlanners::route('/'),
            'create' => CampaignPlannerResource\Pages\CreateCampaignPlanner::route('/create'),
            'edit' => CampaignPlannerResource\Pages\EditCampaignPlanner::route('/{record}/edit'),
        ];
    }
}
