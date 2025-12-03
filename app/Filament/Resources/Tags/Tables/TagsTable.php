<?php

namespace App\Filament\Resources\Tags\Tables;

use App\Models\TagCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        TagCategory::Group->value => 'Group',
                        TagCategory::Nature->value => 'Nature',
                        TagCategory::Focus->value => 'Focus',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        TagCategory::Group->value => 'info',
                        TagCategory::Nature->value => 'success',
                        TagCategory::Focus->value => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projects')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        TagCategory::Group->value => 'Group',
                        TagCategory::Nature->value => 'Nature',
                        TagCategory::Focus->value => 'Focus',
                    ]),
            ])
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
