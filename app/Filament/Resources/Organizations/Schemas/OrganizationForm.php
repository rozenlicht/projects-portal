<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\Organization;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('organizations')
                    ->disk('public')
                    ->maxSize(2048)
                    ->imageEditor(),

                TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->maxLength(255),
            ]);
    }
}
