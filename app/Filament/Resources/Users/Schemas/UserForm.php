<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Group;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                FileUpload::make('avatar_url')
                    ->label('Avatar')
                    ->image()
                    ->directory('avatars')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '1:1',
                    ])
                    ->imageResizeMode('cover')
                    ->imageResizeTargetHeight(100)
                    ->imageResizeTargetWidth(100)
                    ->avatar()
                    ->columnSpanFull(),

                Select::make('group_id')
                    ->label('Group')
                    ->preload()
                    ->relationship('group', 'name')
                    ->searchable(),

                TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Users\Pages\CreateUser)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->minLength(8),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable(),
            ]);
    }
}
