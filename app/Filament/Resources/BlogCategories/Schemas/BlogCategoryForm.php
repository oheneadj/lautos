<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. Import Tips')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // I only auto-fill the slug if it hasn't been manually
                        // overridden, same convention as BlogPostForm uses.
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('auto-generated from name'),
            ]);
    }
}
