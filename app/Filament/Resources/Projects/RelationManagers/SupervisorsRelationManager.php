<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupervisorsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisors';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Supervisor')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => User::find($value)?->name)
                    ->required(),

                TextInput::make('order_rank')
                    ->label('Order')
                    ->numeric()
                    ->default(fn () => ($this->getOwnerRecord()->supervisors()->max('pivot.order_rank') ?? 0) + 1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('pivot.order_rank')
                    ->label('Order')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('project_supervisor.order_rank', $direction);
                    })
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->reorderable('order_rank')
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Supervisor')
                            ->required(),
                        TextInput::make('order_rank')
                            ->label('Order')
                            ->numeric()
                            ->default(fn () => $this->getOwnerRecord()->supervisors()->max('order_rank') + 1 ?? 1)
                            ->required(),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form([
                        TextInput::make('order_rank')
                            ->label('Order')
                            ->numeric()
                            ->required(),
                    ]),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
