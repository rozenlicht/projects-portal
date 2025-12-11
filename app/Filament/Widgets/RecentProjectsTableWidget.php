<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\ProjectType;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentProjectsTableWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->with(['owner.group.section', 'supervisors', 'tags', 'types'])
                    ->latest()
                    ->limit(50)
            )
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(50),

                TextColumn::make('types.name')
                    ->label('Types')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->types->pluck('name')->join(', '))
                    ->color('info'),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supervisors_count')
                    ->counts('supervisors')
                    ->label('Supervisors')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('is_taken')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Taken' : 'Available')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Recent Projects')
            ->description('Latest projects added to the system')
            ->paginated([5, 10])
            ->recordUrl(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record]));
    }
}
