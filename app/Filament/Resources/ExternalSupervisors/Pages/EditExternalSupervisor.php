<?php

namespace App\Filament\Resources\ExternalSupervisors\Pages;

use App\Filament\Resources\ExternalSupervisors\ExternalSupervisorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExternalSupervisor extends EditRecord
{
    protected static string $resource = ExternalSupervisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
