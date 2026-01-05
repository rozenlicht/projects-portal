<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Group;
use App\Models\ProjectType;
use App\Models\Section;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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

                TextColumn::make('types.name')
                    ->label('Types')
                    ->badge()
                    ->formatStateUsing(fn($record) => $record->types->pluck('name')->join(', '))
                    ->color('info'),

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
                SelectFilter::make('scope')
                    ->label('View')
                    ->options([
                        'my_projects' => 'My Projects',
                        'all' => 'All Projects',
                    ])
                    ->default('my_projects')
                    ->query(function ($query, $state) {
                        if ($state === 'my_projects') {
                            $user = Auth::user();
                            return $query->where(function ($q) use ($user) {
                                $q->where('project_owner_id', $user->id)
                                    ->orWhereHas('supervisorLinks', function ($subQ) use ($user) {
                                        $subQ->where('supervisor_type', User::class)
                                            ->where('supervisor_id', $user->id);
                                    });
                            });
                        }
                        return $query;
                    }),

                SelectFilter::make('types')
                    ->label('Type')
                    ->relationship('types', 'name')
                    ->multiple()
                    ->preload(),

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
