<?php

/**
 * @author Ohene Adjei
 */

namespace App\Events;

use App\Models\ContactEnquiry;
use Illuminate\Foundation\Events\Dispatchable;

class ContactEnquirySubmitted
{
    use Dispatchable;

    public function __construct(public ContactEnquiry $enquiry)
    {
    }
}
