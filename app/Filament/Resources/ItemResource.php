<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string { return __('Items'); }
    public static function getModelLabel(): string { return __('Item'); }
    public static function getPluralModelLabel(): string { return __('Items'); }
    public static function getNavigationGroup(): ?string { return __('Operations'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Item details'))->schema([
                TextInput::make('name')->label(__('Name'))->required()->maxLength(150),
                Textarea::make('description')->label(__('Description'))->rows(5)->maxLength(5000)->columnSpanFull(),
                TextInput::make('category')->label(__('Category'))->maxLength(80)->placeholder('e.g. Furniture, Product, Architecture'),
                TagsInput::make('tags')->label(__('Tags'))->separator(',')->placeholder('Add searchable tags'),
                FileUpload::make('image_path')->label(__('Cover image'))->image()->disk('public')->directory('items/images')->imageEditor()->maxSize(10240),
                FileUpload::make('model_path')->label(__('3D model (GLB)'))->acceptedFileTypes(['model/gltf-binary', 'application/octet-stream'])->disk('public')->directory('items/models')->required()->rules(['file', 'mimes:glb', 'max:51200'])->maxSize(51200)->helperText(__('Upload a .glb file. Maximum size: 50 MB. The model is rendered in the preview and public viewer.')),
                Toggle::make('is_published')->label(__('Visible on public gallery'))->default(false),
                Toggle::make('is_featured')->label(__('Featured item'))->helperText(__('Featured items appear first in the public gallery.')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_path')->label('')->disk('public')->square(),
            TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
            TextColumn::make('category')->label(__('Category'))->badge()->sortable(),
            TextColumn::make('view_count')->label(__('Views'))->numeric()->sortable(),
            TextColumn::make('description')->label(__('Description'))->limit(60)->wrap(),
            IconColumn::make('is_published')->label(__('Public'))->boolean(),
            IconColumn::make('is_featured')->label(__('Featured'))->boolean(),
            TextColumn::make('model_validation_status')->label(__('Model check'))->badge()->color(fn (string $state): string => match ($state) {
                'valid' => 'success',
                'invalid' => 'danger',
                default => 'warning',
            }),
            TextColumn::make('user.name')->label(__('Created by'))->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->label(__('Updated'))->dateTime('d M Y')->sortable(),
        ])->actions([
            ViewAction::make()->url(fn (Item $record): string => route('items.show', $record->slug))->openUrlInNewTab()->label(__('Preview')),
            EditAction::make(), DeleteAction::make(),
        ])->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListItems::route('/'), 'create' => Pages\CreateItem::route('/create'), 'edit' => Pages\EditItem::route('/{record}/edit')];
    }
}
