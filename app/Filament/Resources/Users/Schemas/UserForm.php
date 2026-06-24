<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\KycStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Ama Boateng')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->placeholder('e.g. ama@livingstonautos.com')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->placeholder('e.g. +233 24 000 0000')
                            ->maxLength(30),
                        TextInput::make('address')
                            ->placeholder('e.g. Accra, Ghana')
                            ->maxLength(500),
                        TextInput::make('password')
                            ->password()
                            ->placeholder('e.g. ••••••••')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New Password (leave blank to keep)'),
                        Toggle::make('is_admin')
                            ->label('Admin Access')
                            // I default this on since this resource is exclusively for staff
                            // accounts now — leaving it off would create a user that
                            // immediately disappears from this scoped list.
                            ->default(true),
                        // I let a Super Admin assign which staff role this user has —
                        // canAccessPanel() requires both is_admin and at least one role.
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->placeholder('Select one or more roles')
                            ->columnSpanFull(),
                    ]),

                Section::make('KYC Verification')
                    ->columns(2)
                    ->schema([
                        Select::make('kyc_status')
                            ->options(KycStatus::class)
                            ->default(KycStatus::Pending)
                            ->placeholder('Select a status')
                            ->required(),
                        // I show paths read-only — documents are uploaded by the customer, not admin.
                        TextInput::make('ghana_card_path')
                            ->label('Ghana Card (S3 Path)')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('tin_path')
                            ->label('TIN Document (S3 Path)')
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('kyc_notes')
                            ->label('KYC Notes (visible to customer)')
                            ->placeholder('e.g. Please resubmit a clearer photo of your Ghana Card.')
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ]),
            ]);
    }
}
