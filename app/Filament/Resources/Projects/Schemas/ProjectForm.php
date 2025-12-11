<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Organization;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->visible(fn($record) => $record?->id)
                    ->maxLength(255)
                    ->unique(Project::class, 'slug', ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(),

                CheckboxList::make('types')
                    ->relationship('types', 'name')
                    ->required(),

                Select::make('project_owner_id')
                    ->label('Project Owner')
                    ->relationship('owner', 'name')
                    ->default(fn() => auth()->user()?->id)
                    ->required()
                    ->searchable(),

                Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->helperText('The organization the project is associated with. Default is TU/e.')
                    ->default(fn() => Organization::where('name', 'TU/e')->first()?->id)
                    ->required()
                    ->searchable()
                    ->preload(),

                FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->columnSpanFull()
                    ->image()
                    ->directory('projects')
                    ->disk('public')
                    ->maxSize(5120)
                    ->imageEditor(),

                Textarea::make('short_description')
                    ->label('Short Description')
                    ->required()
                    ->rows(3)
                    ->maxLength(500),

                RichEditor::make('richtext_content')
                    ->label('Content')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),

                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record?->name . ' (' . $record?->category?->value . ')')
                    ->columnSpanFull()
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Section::make('Student Information')
                    ->description('If the project is taken, fill in the student information.')
                    ->visible(fn($record) => $record?->id)
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                        TextInput::make('student_name')
                            ->label('Student Name')
                            ->visible(fn($record) => $record?->id)
                            ->maxLength(255),

                        TextInput::make('student_email')
                            ->label('Student Email')
                            ->email()
                            ->visible(fn($record) => $record?->id)
                            ->maxLength(255),
                    ])
            ]);
    }
}
