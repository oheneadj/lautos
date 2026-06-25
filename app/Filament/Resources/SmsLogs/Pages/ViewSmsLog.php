<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SmsLogs\Pages;

use App\Filament\Resources\SmsLogs\SmsLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSmsLog extends ViewRecord
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
