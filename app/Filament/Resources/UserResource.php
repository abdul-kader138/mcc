<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 10;
    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getNavigationLabel(): string { return __('Users'); }
    public static function getModelLabel(): string { return __('User'); }
    public static function getPluralModelLabel(): string { return __('Users'); }
    public static function getNavigationGroup(): ?string { return __('Administration'); }
    public static function getGloballySearchableAttributes(): array { return ['first_name', 'last_name', 'email']; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Account'))->schema([
                TextInput::make('first_name')->label(__('First name'))->required()->maxLength(255),
                TextInput::make('last_name')->label(__('Last name'))->required()->maxLength(255),
                TextInput::make('email')->label(__('Email'))->email()->required()->maxLength(255)->unique(User::class, 'email', ignoreRecord: true),
                Select::make('locale')->label(__('Language'))->options(['en' => __('English'), 'fr' => __('Français')])->placeholder(__('Use system default'))->native(false),
                TextInput::make('password')->label(__('Password'))->password()->revealable()->autocomplete('new-password')->rule(Password::default())->required(fn (string $operation) => $operation === 'create')->dehydrated(fn (?string $state) => filled($state))->dehydrateStateUsing(fn (string $state) => Hash::make($state)),
                TextInput::make('password_confirmation')->label(__('Confirm Password'))->password()->revealable()->dehydrated(false)->same('password')->required(fn (string $operation) => $operation === 'create'),
                Toggle::make('email_verified_at')->label(__('Email verified'))->default(true)->visible(fn (string $operation) => $operation === 'create')->dehydrated(fn (string $operation) => $operation === 'create')->dehydrateStateUsing(fn ($state) => $state ? now() : null),
            ])->columns(2),
            Section::make(__('Roles'))->schema([
                Select::make('roles')->label(__('Roles'))->multiple()->relationship('roles', 'name')->options(fn () => Role::pluck('name', 'id'))->preload()->saveRelationshipsUsing(fn ($record, $state) => $record->syncRoles(Role::findMany($state))),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('first_name')->label(__('First name'))->searchable()->sortable(),
            TextColumn::make('last_name')->label(__('Last name'))->searchable()->sortable(),
            TextColumn::make('email')->label(__('Email'))->searchable()->sortable(),
            TextColumn::make('roles.name')->label(__('Roles'))->badge()->separator(',')->color('primary'),
            TextColumn::make('email_verified_at')->label(__('Verified'))->dateTime()->placeholder(__('Not verified')),
            TextColumn::make('created_at')->label(__('Joined'))->dateTime('d M Y')->sortable(),
        ])->filters([
            SelectFilter::make('roles')->relationship('roles', 'name')->preload(),
        ])->defaultSort('created_at', 'desc')->actions([
            EditAction::make(), DeleteAction::make(),
        ])->bulkActions([
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListUsers::route('/'), 'create' => Pages\CreateUser::route('/create'), 'edit' => Pages\EditUser::route('/{record}/edit')];
    }
}
