<?php

namespace App\Filament\Resources\Groups\Schemas;

use App\Models\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('abbrev_id')
                    ->label('Abbreviation ID')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('section_id')
                    ->label('Section')
                    ->relationship('section', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
