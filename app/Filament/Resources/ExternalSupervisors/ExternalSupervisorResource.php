<?php

namespace App\Filament\Resources\ExternalSupervisors;

use App\Filament\Resources\ExternalSupervisors\Pages\CreateExternalSupervisor;
use App\Filament\Resources\ExternalSupervisors\Pages\EditExternalSupervisor;
use App\Filament\Resources\ExternalSupervisors\Pages\ListExternalSupervisors;
use App\Filament\Resources\ExternalSupervisors\Schemas\ExternalSupervisorForm;
use App\Filament\Resources\ExternalSupervisors\Tables\ExternalSupervisorsTable;
use App\Models\ExternalSupervisor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExternalSupervisorResource extends Resource
{
    protected static ?string $model = ExternalSupervisor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    public static function form(Schema $schema): Schema
    {
        return ExternalSupervisorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExternalSupervisorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExternalSupervisors::route('/'),
            'create' => CreateExternalSupervisor::route('/create'),
            'edit' => EditExternalSupervisor::route('/{record}/edit'),
        ];
    }
}
