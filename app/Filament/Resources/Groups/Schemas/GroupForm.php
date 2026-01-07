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

                Select::make('group_leader_id')
                    ->label('Group Leader')
                    ->relationship('leader', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('external_url')
                    ->label('External URL')
                    ->url()
                    ->maxLength(255)
                    ->helperText('Optional URL that will make the group name clickable on project detail pages.'),
            ]);
    }
}
