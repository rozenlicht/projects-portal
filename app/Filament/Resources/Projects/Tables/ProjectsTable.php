<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Group;
use App\Models\ProjectType;
use App\Models\Section;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        ProjectType::Internship->value => 'Internship',
                        ProjectType::BachelorThesis->value => 'Bachelor Thesis',
                        ProjectType::MasterThesis->value => 'Master Thesis',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        ProjectType::Internship->value => 'info',
                        ProjectType::BachelorThesis->value => 'success',
                        ProjectType::MasterThesis->value => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No organization'),

                TextColumn::make('student_name')
                    ->label('Student')
                    ->placeholder('Available')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        ProjectType::Internship->value => 'Internship',
                        ProjectType::BachelorThesis->value => 'Bachelor Thesis',
                        ProjectType::MasterThesis->value => 'Master Thesis',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'taken' => 'Taken',
                    ])
                    ->query(function ($query, $state) {
                        if ($state === 'available') {
                            return $query->whereNull('student_name')->whereNull('student_email');
                        }
                        if ($state === 'taken') {
                            return $query->where(function ($q) {
                                $q->whereNotNull('student_name')->orWhereNotNull('student_email');
                            });
                        }
                        return $query;
                    })
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
