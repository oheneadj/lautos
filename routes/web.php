<?php

use App\Http\Controllers\Admin\KycDocumentController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\TicketAttachmentController;
use App\Livewire\Auth\CompleteKyc;
use App\Livewire\Customer\Dashboard;
use App\Livewire\Customer\Invoices;
use App\Livewire\Customer\NotificationsHub;
use App\Livewire\Customer\OrderDetail;
use App\Livewire\Customer\OrderList;
use App\Livewire\Customer\ProfileEdit;
use App\Livewire\Customer\Reviews;
use App\Livewire\Customer\SavedCars;
use App\Livewire\Customer\SupportTicketDetail;
use App\Livewire\Customer\SupportTickets;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Car;
use App\Models\Faq;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    SEOMeta::setTitle('Livingston Autos — Quality Japanese & Korean Imports', false);
    SEOMeta::setDescription('Browse quality Japanese & Korean import cars in Ghana, fully inspected and delivered to your door by Livingston Autos.');
    SEOMeta::setCanonical(url('/'));
    OpenGraph::setTitle('Livingston Autos — Quality Japanese & Korean Imports');
    OpenGraph::setDescription('Browse quality Japanese & Korean import cars in Ghana, fully inspected and delivered to your door.');
    TwitterCard::setType('summary_large_image');
    TwitterCard::setTitle('Livingston Autos — Quality Japanese & Korean Imports');
    TwitterCard::setDescription('Browse quality Japanese & Korean import cars in Ghana, fully inspected and delivered to your door.');

    return view('welcome');
})->name('home');

// Cars
Route::get('/cars', function () {
    SEOMeta::setTitle('Cars for Sale | Livingston Autos', false);
    SEOMeta::setDescription('Browse our full inventory of quality Japanese & Korean import cars, with prices in USD and GHS.');
    // I canonicalize every filtered/paginated variant back to the base
    // listing so search engines don't index a duplicate page per query string.
    SEOMeta::setCanonical(url('/cars'));
    OpenGraph::setTitle('Cars for Sale');
    OpenGraph::setDescription('Browse our full inventory of quality Japanese & Korean import cars.');
    TwitterCard::setType('summary_large_image');
    TwitterCard::setTitle('Cars for Sale | Livingston Autos');
    TwitterCard::setDescription('Browse our full inventory of quality Japanese & Korean import cars.');

    return view('pages.cars.index');
})->name('cars.index');

Route::get('/cars/{slug}', function (string $slug) {
    // I use the same visibility scope as the catalogue, not a strict Available
    // check, so a car doesn't 404 the moment someone places an order on it —
    // it should just show as Reserved/unorderable instead of disappearing.
    $car = Car::with(['make', 'carModel', 'carTrim', 'images'])
        ->where('slug', $slug)
        ->visibleOnCatalogue()
        ->firstOrFail();

    $carTitle = "{$car->year} {$car->make->name} {$car->carModel->name}";
    $carPrice = number_format($car->price_usd, 0);

    // I set per-car meta here so each listing is uniquely discoverable in search results.
    SEOMeta::setTitle("{$carTitle} — \${$carPrice} | Livingston Autos", false);
    SEOMeta::setDescription("Buy a {$carTitle} for \${$carPrice} (+ shipping), imported from {$car->country_of_origin} by Livingston Autos. {$car->mileage} km, {$car->transmission}, {$car->fuel_type}.");
    SEOMeta::setCanonical(route('cars.show', $car->slug));
    OpenGraph::setTitle("{$carTitle} — \${$carPrice}");
    OpenGraph::setDescription("Imported from {$car->country_of_origin} by Livingston Autos.");
    if ($car->images->first()) {
        OpenGraph::addImage(Storage::url($car->images->first()->path));
    }
    TwitterCard::setType('summary_large_image');
    TwitterCard::setTitle("{$carTitle} — \${$carPrice}");
    TwitterCard::setDescription("Imported from {$car->country_of_origin} by Livingston Autos.");
    if ($car->images->first()) {
        TwitterCard::setImage(Storage::url($car->images->first()->path));
    }

    // Product structured data so search engines can show price/availability directly in results.
    JsonLd::setType('Product');
    JsonLd::setTitle($carTitle);
    JsonLd::setDescription("Imported from {$car->country_of_origin}. {$car->mileage} km, {$car->transmission}, {$car->fuel_type}.");
    if ($car->images->first()) {
        JsonLd::addImage(Storage::url($car->images->first()->path));
    }
    JsonLd::addValue('offers', [
        '@type' => 'Offer',
        'priceCurrency' => 'USD',
        'price' => number_format($car->price_usd, 2, '.', ''),
        'availability' => 'https://schema.org/InStock',
        'url' => route('cars.show', $car->slug),
    ]);

    // Breadcrumb trail so search results can show Home > Cars > this car
    // instead of just the raw URL.
    JsonLdMulti::setType('BreadcrumbList');
    JsonLdMulti::addValue('itemListElement', [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cars for Sale', 'item' => route('cars.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $carTitle, 'item' => route('cars.show', $car->slug)],
    ]);

    return view('pages.cars.show', compact('car'));
})->name('cars.show');

// Blog
Route::get('/blog', function () {
    SEOMeta::setTitle('Blog | Livingston Autos', false);
    SEOMeta::setDescription('Tips, guides, and news on buying and importing cars to Ghana from Livingston Autos.');
    SEOMeta::setCanonical(url('/blog'));
    OpenGraph::setTitle('Livingston Autos Blog');
    OpenGraph::setDescription('Tips, guides, and news on buying and importing cars to Ghana.');
    TwitterCard::setType('summary_large_image');
    TwitterCard::setTitle('Livingston Autos Blog');
    TwitterCard::setDescription('Tips, guides, and news on buying and importing cars to Ghana.');

    return view('pages.blog.index');
})->name('blog.index');

Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::with(['category', 'author'])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

    SEOMeta::setTitle("{$post->title} | Livingston Autos", false);
    SEOMeta::setDescription($post->excerpt);
    SEOMeta::setCanonical(route('blog.show', $post->slug));
    OpenGraph::setTitle($post->title);
    OpenGraph::setDescription($post->excerpt);
    if ($post->cover_image_path) {
        OpenGraph::addImage(Storage::url($post->cover_image_path));
    }
    TwitterCard::setType('summary_large_image');
    TwitterCard::setTitle($post->title);
    TwitterCard::setDescription($post->excerpt);
    if ($post->cover_image_path) {
        TwitterCard::setImage(Storage::url($post->cover_image_path));
    }

    // Article schema — headline/author/dates straight from the post itself,
    // so this only ever reflects what's actually published.
    JsonLdMulti::setType('Article');
    JsonLdMulti::setTitle($post->title);
    JsonLdMulti::setDescription($post->excerpt);
    if ($post->cover_image_path) {
        JsonLdMulti::addImage(Storage::url($post->cover_image_path));
    }
    JsonLdMulti::addValue('headline', $post->title);
    JsonLdMulti::addValue('datePublished', $post->published_at?->toIso8601String());
    JsonLdMulti::addValue('dateModified', $post->updated_at->toIso8601String());
    if ($post->author) {
        JsonLdMulti::addValue('author', ['@type' => 'Person', 'name' => $post->author->name]);
    }

    // Breadcrumb trail: Home > Blog > this post.
    JsonLdMulti::newJsonLd();
    JsonLdMulti::setType('BreadcrumbList');
    JsonLdMulti::addValue('itemListElement', [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => route('blog.show', $post->slug)],
    ]);

    $latestNews = BlogPost::published()
        ->where('id', '!=', $post->id)
        ->latest('published_at')
        ->limit(3)
        ->get();

    $featuredStories = BlogPost::published()
        ->where('id', '!=', $post->id)
        ->inRandomOrder()
        ->limit(3)
        ->get();

    $categories = BlogCategory::has('posts')
        ->withCount(['posts' => fn ($q) => $q->published()])
        ->orderBy('name')
        ->get();

    return view('pages.blog.show', compact('post', 'latestNews', 'featuredStories', 'categories'));
})->name('blog.show');

// Contact
Route::get('/contact', function () {
    SEOMeta::setTitle('Contact Us | Livingston Autos', false);
    SEOMeta::setDescription('Reach Livingston Autos by phone, email, or WhatsApp — we reply within 24 hours.');
    SEOMeta::setCanonical(url('/contact'));
    OpenGraph::setTitle('Contact Livingston Autos');
    OpenGraph::setDescription('Reach us by phone, email, or WhatsApp — we reply within 24 hours.');
    TwitterCard::setType('summary');
    TwitterCard::setTitle('Contact Livingston Autos');
    TwitterCard::setDescription('Reach us by phone, email, or WhatsApp — we reply within 24 hours.');

    return view('pages.contact');
})->name('contact');

// Static pages
Route::get('/about', function () {
    SEOMeta::setTitle('About Us | Livingston Autos', false);
    SEOMeta::setDescription('Livingston Autos imports quality used vehicles from Japan and Korea to Ghana, with full shipment tracking and offline payment support.');
    SEOMeta::setCanonical(url('/about'));
    OpenGraph::setTitle('About Livingston Autos');
    OpenGraph::setDescription('Quality Japanese & Korean imports, delivered to your door.');
    TwitterCard::setType('summary');
    TwitterCard::setTitle('About Livingston Autos');
    TwitterCard::setDescription('Quality Japanese & Korean imports, delivered to your door.');

    return view('pages.about');
})->name('about');

Route::get('/payment-info', function () {
    SEOMeta::setTitle('Payment Information | Livingston Autos', false);
    SEOMeta::setDescription('Bank transfer and Mobile Money details for paying for your car, plus step-by-step payment instructions.');
    SEOMeta::setCanonical(url('/payment-info'));
    OpenGraph::setTitle('Payment Information');
    OpenGraph::setDescription('Bank transfer and Mobile Money details for paying for your car.');
    TwitterCard::setType('summary');
    TwitterCard::setTitle('Payment Information');
    TwitterCard::setDescription('Bank transfer and Mobile Money details for paying for your car.');

    return view('pages.payment-info');
})->name('pages.payment-info');

Route::get('/how-it-works', function () {
    SEOMeta::setTitle('How It Works — Importing a Car | Livingston Autos', false);
    SEOMeta::setDescription('Importing a car with Livingston Autos is straightforward and transparent — we handle the sourcing, and you can track your vehicle every step of the way.');
    SEOMeta::setCanonical(url('/how-it-works'));
    OpenGraph::setTitle('How It Works — Importing a Car with Livingston Autos');
    OpenGraph::setDescription('Importing a car with Livingston Autos is straightforward and transparent.');

    return view('pages.how-it-works');
})->name('pages.how-it-works');

Route::get('/shipping-and-delivery', function () {
    SEOMeta::setTitle('Shipping & Delivery | Livingston Autos', false);
    SEOMeta::setDescription("We work with the world's leading RoRo and container shipping lines, with marine insurance, to get your vehicle to Tema Port reliably.");
    SEOMeta::setCanonical(url('/shipping-and-delivery'));
    OpenGraph::setTitle('Reliable Shipping Logistics');
    OpenGraph::setDescription("We work with the world's leading RoRo and container shipping lines.");

    return view('pages.shipping');
})->name('pages.shipping');

Route::get('/customs-clearance', function () {
    SEOMeta::setTitle('Customs Clearance Guide | Livingston Autos', false);
    SEOMeta::setDescription('When your vehicle arrives in Ghana it must undergo customs clearance at Tema Port — see our two hassle-free clearance options.');
    SEOMeta::setCanonical(url('/customs-clearance'));
    OpenGraph::setTitle('Clearing Your Vehicle at Tema Port');
    OpenGraph::setDescription('Two hassle-free clearance options for your vehicle at Tema Port.');

    return view('pages.customs-clearance');
})->name('pages.customs-clearance');

Route::get('/quality-guarantee', function () {
    SEOMeta::setTitle('Vehicle Inspections & Quality Guarantee | Livingston Autos', false);
    SEOMeta::setDescription('We partner directly with established auction houses and independent mechanics in Japan and South Korea, removing middlemen to maintain control over quality.');
    SEOMeta::setCanonical(url('/quality-guarantee'));
    OpenGraph::setTitle('Our Unbreakable Promise to You');
    OpenGraph::setDescription('Direct auction access, independent mechanics, and rigorous quality standards.');

    return view('pages.quality-guarantee');
})->name('pages.quality-guarantee');

Route::get('/faqs', function () {
    $faqs = Faq::ordered()->get();

    SEOMeta::setTitle('Frequently Asked Questions | Livingston Autos', false);
    SEOMeta::setDescription('Find quick answers to common questions about importing your car to Ghana with Livingston Autos.');
    SEOMeta::setCanonical(url('/faqs'));
    OpenGraph::setTitle('Frequently Asked Questions — We\'re Here to Help');
    OpenGraph::setDescription('Find quick answers to common questions about importing your car to Ghana.');

    // FAQPage schema built straight from the same Faq rows the page itself
    // renders, so the structured data can never drift from what's visible.
    if ($faqs->isNotEmpty()) {
        JsonLdMulti::setType('FAQPage');
        JsonLdMulti::addValue('mainEntity', $faqs->map(fn (Faq $faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq->answer,
            ],
        ])->all());
    }

    return view('pages.faqs', compact('faqs'));
})->name('pages.faqs');

Route::get('/refund-policy', function () {
    SEOMeta::setTitle('Refund Policy | Livingston Autos', false);
    SEOMeta::setDescription('Understand our policies regarding order cancellations and refunds before committing to a purchase.');
    SEOMeta::setCanonical(url('/refund-policy'));
    OpenGraph::setTitle('Refund Policy — Fair & Simple');
    OpenGraph::setDescription('Understand our policies regarding order cancellations and refunds.');

    return view('pages.refund-policy');
})->name('pages.refund-policy');

Route::get('/terms-and-conditions', function () {
    SEOMeta::setTitle('Terms & Conditions | Livingston Autos', false);
    SEOMeta::setDescription('Please read these terms carefully before using our services or purchasing a vehicle from Livingston Autos.');
    SEOMeta::setCanonical(url('/terms-and-conditions'));
    OpenGraph::setTitle('Terms & Conditions — Clear & Transparent');
    OpenGraph::setDescription('Please read these terms carefully before using our services.');

    return view('pages.terms');
})->name('pages.terms');

Route::get('/privacy-policy', function () {
    SEOMeta::setTitle('Privacy Policy | Livingston Autos', false);
    SEOMeta::setDescription('We respect your privacy and are committed to protecting your personal data.');
    SEOMeta::setCanonical(url('/privacy-policy'));
    OpenGraph::setTitle('Privacy Policy — Your Data is Secure');
    OpenGraph::setDescription('We respect your privacy and are committed to protecting your personal data.');

    return view('pages.privacy');
})->name('pages.privacy');

Route::get('/fraud-awareness', function () {
    SEOMeta::setTitle('Fraud Awareness | Livingston Autos', false);
    SEOMeta::setDescription('Important information to help you identify and avoid fraudulent sellers and scams when importing a car.');
    SEOMeta::setCanonical(url('/fraud-awareness'));
    OpenGraph::setTitle('Fraud Awareness — Protect Yourself');
    OpenGraph::setDescription('Important information to help you identify and avoid fraudulent sellers and scams.');

    return view('pages.fraud-awareness');
})->name('pages.fraud-awareness');

// Google OAuth — "Continue with Google" on login/register
Route::middleware(['guest'])->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
});

// Connecting Google to an already-authenticated account, from Security settings.
Route::middleware(['auth'])->group(function () {
    Route::get('/auth/google/link', [GoogleAuthController::class, 'redirectToLink'])->name('auth.google.link');
});

// The callback has to be reachable by both flows above, so it can't sit
// behind 'guest' — it checks Auth::check() itself to tell them apart.
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Authenticated customer area
Route::middleware(['auth'])->group(function () {
    // We don't force 'verified' on the whole dashboard because we want
    // users to be able to complete KYC and see the dashboard alerts.
    // They are just restricted from certain actions if unverified.

    // Auth & Registration Flow
    Route::get('/register/kyc', CompleteKyc::class)->name('register.kyc');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', Dashboard::class)->name('index');
        Route::get('/orders', OrderList::class)->name('orders');
        Route::get('/orders/{order:uuid}', OrderDetail::class)->name('orders.show');
        Route::get('/profile', ProfileEdit::class)->name('profile');

        // New Features
        Route::get('/saved-cars', SavedCars::class)->name('saved-cars');
        Route::get('/invoices', Invoices::class)->name('invoices');
        Route::get('/support', SupportTickets::class)->name('support');
        Route::get('/support/{uuid}', SupportTicketDetail::class)->name('support.show');
        Route::get('/notifications', NotificationsHub::class)->name('notifications');
        Route::get('/reviews', Reviews::class)->name('reviews');
    });
});

// KYC document previews — only reachable via a signed, short-lived URL generated
// by the admin Customer detail page. Never link the storage path directly.
Route::get('/admin/kyc-documents/{user:uuid}/{type}', [KycDocumentController::class, 'show'])
    ->middleware(['signed', 'auth'])
    ->name('admin.kyc-documents.show');

// Payment proof previews — same pattern as the KYC route above.
Route::get('/admin/payment-proofs/{proof:uuid}', [PaymentProofController::class, 'show'])
    ->middleware(['signed', 'auth'])
    ->name('admin.payment-proofs.show');

// Support ticket attachment previews — same signed-URL pattern, but reachable
// by either the ticket's own customer or an admin, since it's a two-way thread.
Route::get('/ticket-attachments/{message:uuid}', [TicketAttachmentController::class, 'show'])
    ->middleware(['signed', 'auth'])
    ->name('ticket-attachments.show');

require __DIR__.'/settings.php';
