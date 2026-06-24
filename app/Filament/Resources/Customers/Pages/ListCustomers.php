<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Enums\KycStatus;
use App\Filament\Resources\Customers\CustomerResource;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    /** I add a tab per KycStatus so admins can jump straight to customers needing review. */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (KycStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->modifyQueryUsing(fn ($query) => $query->where('kyc_status', $status))
                // I scope the count to is_admin = false same as CustomerResource's base
                // query, otherwise an admin account with a stray kyc_status would inflate it.
                ->badge(User::where('is_admin', false)->where('kyc_status', $status)->count())
                ->badgeColor($status->colour());
        }

        return $tabs;
    }
}
