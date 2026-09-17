<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotspotResource\Pages;
use App\Models\Hotspot;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HotspotResource extends Resource
{
    protected static ?string $model = Hotspot::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string { return __('Hotspots'); }
    public static function getModelLabel(): string { return __('Hotspot'); }
    public static function getPluralModelLabel(): string { return __('Hotspots'); }
    public static function getNavigationGroup(): ?string { return __('Operations'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('item_id')
                ->label(__('Model'))
                ->relationship('item', 'name')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('title')->label(__('Title'))->required()->maxLength(120),
            Textarea::make('description')->label(__('Description'))->rows(4)->maxLength(1000)->columnSpanFull(),
            TextInput::make('position_x')->label(__('X position'))->numeric()->step('0.01')->default(0)->helperText(__('Normalized model coordinate, usually between -1 and 1.')),
            TextInput::make('position_y')->label(__('Y position'))->numeric()->step('0.01')->default(0)->helperText(__('Normalized model coordinate, usually between -1 and 1.')),
            TextInput::make('position_z')->label(__('Z position'))->numeric()->step('0.01')->default(0)->helperText(__('Normalized model coordinate, usually between -1 and 1.')),
            TextInput::make('sort_order')->label(__('Display order'))->numeric()->integer()->default(0),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
            TextColumn::make('item.name')->label(__('Model'))->searchable()->sortable(),
            TextColumn::make('description')->label(__('Description'))->limit(70),
            TextColumn::make('sort_order')->label(__('Order'))->sortable(),
            TextColumn::make('updated_at')->label(__('Updated'))->dateTime('d M Y')->sortable(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])->bulkActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotspots::route('/'),
            'create' => Pages\CreateHotspot::route('/create'),
            'edit' => Pages\EditHotspot::route('/{record}/edit'),
        ];
    }
}
