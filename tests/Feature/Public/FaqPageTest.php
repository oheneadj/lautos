<?php

namespace Tests\Feature\Public;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests the public FAQ page now reads from the database instead of
 * hardcoded markup.
 */
class FaqPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_faqs_from_the_database_in_order(): void
    {
        Faq::create(['question' => 'Second question?', 'answer' => 'Second answer.', 'sort_order' => 2]);
        Faq::create(['question' => 'First question?', 'answer' => 'First answer.', 'sort_order' => 1]);

        $response = $this->get(route('pages.faqs'));

        $response->assertOk()
            ->assertSeeInOrder(['First question?', 'Second question?'])
            ->assertSee('First answer.')
            ->assertSee('Second answer.');
    }

    #[Test]
    public function it_shows_no_faqs_gracefully_when_none_exist(): void
    {
        $this->get(route('pages.faqs'))->assertOk();
    }
}
