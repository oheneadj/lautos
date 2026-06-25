<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Makes;

use App\Filament\Resources\Makes\Pages\EditMake;
use App\Filament\Resources\Makes\Pages\ListMakes;
use App\Filament\Resources\Makes\RelationManagers\CarModelsRelationManager;
use App\Filament\Resources\Makes\Schemas\MakeForm;
use App\Filament\Resources\Makes\Tables\MakesTable;
use App\Models\Make;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MakeResource extends Resource
{
    protected static ?string $model = Make::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MakeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MakesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CarModelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        // Create still opens as a modal on the list page, but edit now needs
        // a real page so the models/trims relation manager has somewhere to render.
        return [
            'index' => ListMakes::route('/'),
            'edit' => EditMake::route('/{record}/edit'),
        ];
    }
}
