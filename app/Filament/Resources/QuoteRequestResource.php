<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Models\QuoteRequest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 9;

    public static function getNavigationLabel(): string { return __('Quote requests'); }
    public static function getModelLabel(): string { return __('Quote request'); }
    public static function getPluralModelLabel(): string { return __('Quote requests'); }
    public static function getNavigationGroup(): ?string { return __('Operations'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')->label(__('Status'))->options([
                'new' => __('New'),
                'in_progress' => __('In progress'),
                'quoted' => __('Quoted'),
                'closed' => __('Closed'),
            ])->required(),
            TextInput::make('name')->label(__('Name'))->disabled(),
            TextInput::make('email')->label(__('Email'))->disabled(),
            TextInput::make('company')->label(__('Company'))->disabled(),
            Textarea::make('message')->label(__('Message'))->disabled()->columnSpanFull(),
            Textarea::make('configuration')->label(__('Configuration snapshot'))->disabled()->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
            TextColumn::make('email')->label(__('Email'))->searchable(),
            TextColumn::make('item.name')->label(__('Model'))->searchable()->sortable(),
            TextColumn::make('status')->label(__('Status'))->badge()->sortable(),
            TextColumn::make('created_at')->label(__('Received'))->dateTime('d M Y H:i')->sortable(),
        ])->defaultSort('created_at', 'desc')->actions([
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
