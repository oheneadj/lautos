<?php

/**
 * Seeds the FAQs that used to be hardcoded directly in faqs.blade.php, so
 * the public page has real content to show once it switched to reading
 * from the database.
 *
 * @author Ohene Adjei
 */

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How long does shipping take?',
                'answer' => 'Shipping from South Korea typically takes 4-6 weeks, while shipments from Japan take 5-7 weeks. These timelines account for vessel loading, ocean transit, and arrival at Tema Port.',
            ],
            [
                'question' => 'Do prices include customs duties?',
                'answer' => 'Unless explicitly stated on the vehicle listing as "Duty Paid", our prices only cover the cost of the vehicle and shipping to Tema port (C&F). Customs duties must be paid to the Ghana Revenue Authority (GRA) upon arrival.',
            ],
            [
                'question' => 'Are the vehicles inspected before shipping?',
                'answer' => 'Yes. Every single vehicle undergoes a rigorous point-by-point inspection verifying the engine, transmission, chassis integrity, and interior quality before it is approved for export to Ghana.',
            ],
            [
                'question' => 'How do I make a payment?',
                'answer' => 'Once you place an order, you can complete your payment safely offline via Bank Transfer or Mobile Money. You then upload your proof of payment directly to your customer dashboard for verification.',
            ],
            [
                'question' => 'Do you assist with customs clearance?',
                'answer' => 'We provide two options: You can choose to self-clear using your own GRA-certified agent, or you can opt for our Managed Doorstep Delivery, where our team handles the entire clearing process for you.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer'], 'sort_order' => $index]
            );
        }
    }
}
