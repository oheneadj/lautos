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
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('address')
                            ->maxLength(500),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New Password (leave blank to keep)'),
                        Toggle::make('is_admin')
                            ->label('Admin Access'),
                        // I let a Super Admin assign which staff role this user has —
                        // canAccessPanel() requires both is_admin and at least one role.
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                    ]),

                Section::make('KYC Verification')
                    ->columns(2)
                    ->schema([
                        Select::make('kyc_status')
                            ->options(KycStatus::class)
                            ->default(KycStatus::Pending)
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
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ]),
            ]);
    }
}
