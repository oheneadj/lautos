<?php

namespace Tests\Unit\Listeners;

use App\Events\ContactEnquirySubmitted;
use App\Listeners\SendContactEnquiryNotification;
use App\Models\ContactEnquiry;
use App\Models\Setting;
use App\Notifications\ContactEnquiryReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that a submitted contact enquiry actually notifies the configured
 * business inbox (T-32-3).
 */
class SendContactEnquiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_notifies_the_configured_contact_email(): void
    {
        Notification::fake();
        Setting::set('contact_email', 'mrseth@livingstonautos.com');

        $enquiry = ContactEnquiry::create([
            'name' => 'Kofi Mensah',
            'email' => 'kofi@example.com',
            'subject' => 'General Enquiry',
            'message' => 'Test message',
        ]);

        (new SendContactEnquiryNotification())->handle(new ContactEnquirySubmitted($enquiry));

        Notification::assertSentOnDemand(
            ContactEnquiryReceivedNotification::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'mrseth@livingstonautos.com'
        );
    }

    #[Test]
    public function it_does_nothing_when_no_contact_email_is_configured(): void
    {
        Notification::fake();
        Setting::set('contact_email', '');

        $enquiry = ContactEnquiry::create([
            'name' => 'Kofi Mensah',
            'email' => 'kofi@example.com',
            'subject' => 'General Enquiry',
            'message' => 'Test message',
        ]);

        (new SendContactEnquiryNotification())->handle(new ContactEnquirySubmitted($enquiry));

        Notification::assertNothingSent();
    }
}
