<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Models\Tag;
use App\Models\TagCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(Tag::class, 'name', ignoreRecord: true),

                Select::make('category')
                    ->options([
                        TagCategory::Group->value => 'Group',
                        TagCategory::Nature->value => 'Nature',
                        TagCategory::Focus->value => 'Focus',
                    ])
                    ->required()
                    ->native(false),
            ]);
    }
}
