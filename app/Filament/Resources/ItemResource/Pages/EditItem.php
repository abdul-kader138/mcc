<?php
namespace App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource;
use App\Services\ModelValidationService;
use Filament\Resources\Pages\EditRecord;
class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('model_path')) {
            app(ModelValidationService::class)->validateAndStore($this->record);
        }
    }
}
