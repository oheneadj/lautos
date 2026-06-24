<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Reviews;

use App\Enums\ReviewStatus;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Tables\ReviewsTable;
use App\Models\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 3;

    // I route by uuid so the integer id is never exposed in the URL.
    protected static ?string $recordRouteKeyName = 'uuid';

    public static function table(Table $table): Table
    {
        return ReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
        ];
    }

    /**
     * I badge the nav item with the count of reviews awaiting moderation,
     * so staff know there's something to action without opening the list.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', ReviewStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
