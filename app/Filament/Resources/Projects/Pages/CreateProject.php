<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions()
        ];
    }

    public function saveAsConcept(): void
    {
        $this->form->fill([
            'publication_status' => PublicationStatus::Concept->value,
        ]);

        $this->create();
    }
}
