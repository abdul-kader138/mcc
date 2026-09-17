<?php

namespace App\Filament\Resources\HotspotResource\Pages;

use App\Filament\Resources\HotspotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHotspots extends ListRecords
{
    protected static string $resource = HotspotResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
