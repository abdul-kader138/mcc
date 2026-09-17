<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TextureResource\Pages;
use App\Models\Texture;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TextureResource extends Resource
{
    protected static ?string $model = Texture::class;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string { return __('Textures'); }
    public static function getModelLabel(): string { return __('Texture'); }
    public static function getPluralModelLabel(): string { return __('Textures'); }
    public static function getNavigationGroup(): ?string { return __('Operations'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label(__('Texture name'))->required()->maxLength(120)->placeholder('Walnut wood'),
            Select::make('item_id')
                ->label(__('Apply to model'))
                ->relationship('item', 'name')
                ->searchable()
                ->preload()
                ->placeholder(__('All public models'))
                ->helperText(__('Leave empty to make this texture available on every model.')),
            FileUpload::make('path')
                ->label(__('Texture image'))
                ->image()
                ->required()
                ->disk('public')
                ->directory('textures')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->helperText(__('Upload a seamless JPG, PNG, or WebP image.')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('path')->label('')->disk('public')->square(),
            TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
            TextColumn::make('item.name')->label(__('Model'))->placeholder(__('All public models'))->searchable(),
            TextColumn::make('created_at')->label(__('Created'))->dateTime('d M Y')->sortable(),
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
            'index' => Pages\ListTextures::route('/'),
            'create' => Pages\CreateTexture::route('/create'),
            'edit' => Pages\EditTexture::route('/{record}/edit'),
        ];
    }
}
