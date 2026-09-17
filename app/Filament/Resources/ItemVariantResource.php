<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemVariantResource\Pages;
use App\Models\ItemVariant;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemVariantResource extends Resource
{
    protected static ?string $model = ItemVariant::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?int $navigationSort = 8;

    public static function getNavigationLabel(): string { return __('Variants'); }
    public static function getModelLabel(): string { return __('Variant'); }
    public static function getPluralModelLabel(): string { return __('Variants'); }
    public static function getNavigationGroup(): ?string { return __('Operations'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('item_id')->label(__('Model'))->relationship('item', 'name')->required()->searchable()->preload(),
            TextInput::make('name')->label(__('Variant name'))->required()->maxLength(120),
            TextInput::make('sku')->label(__('SKU'))->maxLength(80),
            TextInput::make('price')->label(__('Price'))->numeric()->prefix('$')->step('0.01'),
            Textarea::make('description')->label(__('Description'))->rows(3)->maxLength(1000)->columnSpanFull(),
            Textarea::make('configuration')
                ->label(__('Appearance preset JSON'))
                ->rows(8)
                ->rules(['nullable', 'json'])
                ->helperText(__('Optional JSON mapping mesh indexes to colors/textures. Example: {\"parts\":{\"0\":{\"color\":\"#111827\"}}}'))
                ->columnSpanFull(),
            Checkbox::make('is_default')->label(__('Default variant')),
            TextInput::make('sort_order')->label(__('Display order'))->numeric()->integer()->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
            TextColumn::make('item.name')->label(__('Model'))->searchable()->sortable(),
            TextColumn::make('sku')->label(__('SKU'))->placeholder('—'),
            TextColumn::make('price')->label(__('Price'))->money('USD')->sortable(),
            IconColumn::make('is_default')->label(__('Default'))->boolean(),
            TextColumn::make('sort_order')->label(__('Order'))->sortable(),
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
            'index' => Pages\ListItemVariants::route('/'),
            'create' => Pages\CreateItemVariant::route('/create'),
            'edit' => Pages\EditItemVariant::route('/{record}/edit'),
        ];
    }
}
