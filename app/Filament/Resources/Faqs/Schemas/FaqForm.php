<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->required()
                    ->placeholder('e.g. How long does shipping take?')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('answer')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Display Order')
                    ->helperText('Lower numbers show first on the public FAQ page.')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
