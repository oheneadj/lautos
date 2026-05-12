<?php

namespace App\Filament\Resources\ContactEnquiries\Pages;

use App\Filament\Resources\ContactEnquiries\ContactEnquiryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactEnquiry extends CreateRecord
{
    protected static string $resource = ContactEnquiryResource::class;
}
