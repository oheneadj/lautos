<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\ContactEnquiries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactEnquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Enquiry')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('phone')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('subject')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('message')
                            ->required()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Toggle::make('is_read')
                            ->label('Mark as read'),
                    ]),
            ]);
    }
}
