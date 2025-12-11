<?php

namespace App\Filament\Resources\ExternalSupervisors\Pages;

use App\Filament\Resources\ExternalSupervisors\ExternalSupervisorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExternalSupervisors extends ListRecords
{
    protected static string $resource = ExternalSupervisorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
