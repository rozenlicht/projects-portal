<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('saveAsConcept')
                ->label('Save as concept')
                ->color('gray')
                ->action('saveAsConcept'),
        ];
    }

    public function saveAsConcept(): void
    {
        $this->form->fill([
            'publication_status' => PublicationStatus::Concept->value,
        ]);

        $this->save();
    }
}
