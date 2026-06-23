<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Widgets;

use App\Enums\KycStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActionRequiredWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('ViewAny:Order') ?? false;
    }

    protected function getStats(): array
    {
        $actionOrders = Order::where('status', OrderStatus::PaymentUploaded)->count();
        $kycVerified = User::where('kyc_status', KycStatus::Verified)->count();
        $kycUnverified = User::where('kyc_status', '!=', KycStatus::Verified)->count();

        return [
            Stat::make('Orders Requiring Action', $actionOrders)
                ->description('Awaiting payment confirmation')
                ->color($actionOrders > 0 ? 'warning' : 'success'),
            Stat::make('KYC Verified Users', $kycVerified)
                ->description('Fully verified customers')
                ->color('success'),
            Stat::make('KYC Unverified Users', $kycUnverified)
                ->description('Pending review or resubmission')
                ->color($kycUnverified > 0 ? 'warning' : 'gray'),
        ];
    }
}
