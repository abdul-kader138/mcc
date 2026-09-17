<?php
namespace App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource;
use App\Services\ModelValidationService;
use Filament\Resources\Pages\CreateRecord;
class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        app(ModelValidationService::class)->validateAndStore($this->record);
    }
}
