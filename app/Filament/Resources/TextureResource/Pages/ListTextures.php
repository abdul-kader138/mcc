<?php

namespace App\Filament\Resources\TextureResource\Pages;

use App\Filament\Resources\TextureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTextures extends ListRecords
{
    protected static string $resource = TextureResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
