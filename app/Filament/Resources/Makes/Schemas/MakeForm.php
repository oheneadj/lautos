<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Makes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MakeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. Toyota')
                            ->maxLength(100)
                            ->columnSpanFull(),

                        FileUpload::make('icon_path')
                            ->label('Brand Icon')
                            ->image()
                            ->imagePreviewHeight('80')
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp'])
                            ->maxSize(512) // 512 KB — icons should be small
                            ->disk('public')
                            ->directory('make-icons')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
