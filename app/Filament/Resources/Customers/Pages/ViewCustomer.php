<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Customers\Pages;

use App\Enums\KycStatus;
use App\Filament\Resources\Customers\CustomerResource;
use App\Services\KycService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use InvalidArgumentException;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->getRecord())
            ->components([
                Grid::make(1)
                    ->schema([
                        Section::make('Profile')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('phone')->placeholder('Not provided'),
                                TextEntry::make('address')->placeholder('Not provided'),
                                TextEntry::make('created_at')->label('Registered')->since(),
                            ])->columns(2),
                        Section::make('KYC')
                            ->schema([
                                TextEntry::make('kyc_status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (KycStatus $state) => $state->colour()),
                                TextEntry::make('ghana_card_number')->label('Ghana Card #')->placeholder('Not provided'),
                                TextEntry::make('tin_number')->label('TIN')->placeholder('Not provided'),
                                TextEntry::make('kyc_notes')->label('Admin Notes')->placeholder('—'),
                            ])->columns(2),
                    ]),

                Section::make('KYC Documents')
                    ->schema([
                        ViewEntry::make('documents')
                            ->view('filament.infolists.kyc-documents')
                            ->viewData(['customer' => $this->getRecord()]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $customer = $this->getRecord();

        return [
            Action::make('verifyKyc')
                ->label('Verify KYC')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->visible(fn () => $customer->kyc_status !== KycStatus::Verified)
                ->requiresConfirmation()
                ->action(function () use ($customer) {
                    try {
                        app(KycService::class)->verify($customer);
                    } catch (InvalidArgumentException $e) {
                        Notification::make()->danger()->title($e->getMessage())->send();

                        return;
                    }

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $customer]));
                }),

            Action::make('requestResubmission')
                ->label('Request Resubmission')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('danger')
                ->visible(fn () => $customer->kyc_status !== KycStatus::NeedsResubmission)
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('reason')
                        ->label('Reason')
                        ->placeholder('e.g. The Ghana Card photo is blurry — please resubmit a clearer copy.')
                        ->required(),
                ])
                ->action(function (array $data) use ($customer) {
                    app(KycService::class)->requestResubmission($customer, $data['reason']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $customer]));
                }),
        ];
    }
}
