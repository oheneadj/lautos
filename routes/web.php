<?php

use App\Models\BlogPost;
use App\Models\Car;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('Livingston Autos — Quality Japanese & Korean Imports', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Browse quality Japanese & Korean import cars in Ghana, fully inspected and delivered to your door by Livingston Autos.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('Livingston Autos — Quality Japanese & Korean Imports');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Browse quality Japanese & Korean import cars in Ghana, fully inspected and delivered to your door.');

    return view('welcome');
})->name('home');

// Cars
Route::get('/cars', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('Cars for Sale | Livingston Autos', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Browse our full inventory of quality Japanese & Korean import cars, with prices in USD and GHS.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('Cars for Sale');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Browse our full inventory of quality Japanese & Korean import cars.');

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
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle("{$carTitle} — \${$carPrice} | Livingston Autos", false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription("Buy a {$carTitle} for \${$carPrice} (+ shipping), imported from {$car->country_of_origin} by Livingston Autos. {$car->mileage} km, {$car->transmission}, {$car->fuel_type}.");
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle("{$carTitle} — \${$carPrice}");
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription("Imported from {$car->country_of_origin} by Livingston Autos.");
    if ($car->images->first()) {
        \Artesaos\SEOTools\Facades\OpenGraph::addImage(\Illuminate\Support\Facades\Storage::url($car->images->first()->path));
    }

    // Product structured data so search engines can show price/availability directly in results.
    \Artesaos\SEOTools\Facades\JsonLd::setType('Product');
    \Artesaos\SEOTools\Facades\JsonLd::setTitle($carTitle);
    \Artesaos\SEOTools\Facades\JsonLd::setDescription("Imported from {$car->country_of_origin}. {$car->mileage} km, {$car->transmission}, {$car->fuel_type}.");
    if ($car->images->first()) {
        \Artesaos\SEOTools\Facades\JsonLd::addImage(\Illuminate\Support\Facades\Storage::url($car->images->first()->path));
    }
    \Artesaos\SEOTools\Facades\JsonLd::addValue('offers', [
        '@type' => 'Offer',
        'priceCurrency' => 'USD',
        'price' => number_format($car->price_usd, 2, '.', ''),
        'availability' => 'https://schema.org/InStock',
        'url' => route('cars.show', $car->slug),
    ]);

    return view('pages.cars.show', compact('car'));
})->name('cars.show');

// Blog
Route::get('/blog', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('Blog | Livingston Autos', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Tips, guides, and news on buying and importing cars to Ghana from Livingston Autos.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('Livingston Autos Blog');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Tips, guides, and news on buying and importing cars to Ghana.');

    return view('pages.blog.index');
})->name('blog.index');

Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::with(['category', 'author'])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

    \Artesaos\SEOTools\Facades\SEOMeta::setTitle("{$post->title} | Livingston Autos", false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription($post->excerpt);
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle($post->title);
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription($post->excerpt);
    if ($post->cover_image_path) {
        \Artesaos\SEOTools\Facades\OpenGraph::addImage(\Illuminate\Support\Facades\Storage::url($post->cover_image_path));
    }

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

    return view('pages.blog.show', compact('post', 'latestNews', 'featuredStories'));
})->name('blog.show');

// Contact
Route::get('/contact', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('Contact Us | Livingston Autos', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Reach Livingston Autos by phone, email, or WhatsApp — we reply within 24 hours.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('Contact Livingston Autos');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Reach us by phone, email, or WhatsApp — we reply within 24 hours.');

    return view('pages.contact');
})->name('contact');

// Static pages
Route::get('/about', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('About Us | Livingston Autos', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Livingston Autos imports quality used vehicles from Japan and Korea to Ghana, with full shipment tracking and offline payment support.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('About Livingston Autos');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Quality Japanese & Korean imports, delivered to your door.');

    return view('pages.about');
})->name('about');

Route::get('/payment-info', function () {
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle('Payment Information | Livingston Autos', false);
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription('Bank transfer and Mobile Money details for paying for your car, plus step-by-step payment instructions.');
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle('Payment Information');
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription('Bank transfer and Mobile Money details for paying for your car.');

    return view('pages.payment-info');
})->name('pages.payment-info');

Route::view('/how-it-works', 'pages.how-it-works')->name('pages.how-it-works');
Route::view('/shipping-and-delivery', 'pages.shipping')->name('pages.shipping');
Route::view('/customs-clearance', 'pages.customs-clearance')->name('pages.customs-clearance');
Route::view('/quality-guarantee', 'pages.quality-guarantee')->name('pages.quality-guarantee');
Route::view('/faqs', 'pages.faqs')->name('pages.faqs');
Route::view('/refund-policy', 'pages.refund-policy')->name('pages.refund-policy');
Route::view('/terms-and-conditions', 'pages.terms')->name('pages.terms');
Route::view('/privacy-policy', 'pages.privacy')->name('pages.privacy');
Route::view('/fraud-awareness', 'pages.fraud-awareness')->name('pages.fraud-awareness');

// Google OAuth — "Continue with Google" on login/register
Route::middleware(['guest'])->group(function () {
    Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// Authenticated customer area
Route::middleware(['auth'])->group(function () {
    // We don't force 'verified' on the whole dashboard because we want
    // users to be able to complete KYC and see the dashboard alerts.
    // They are just restricted from certain actions if unverified.

    // Auth & Registration Flow
    Route::get('/register/kyc', \App\Livewire\Auth\CompleteKyc::class)->name('register.kyc');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', \App\Livewire\Customer\Dashboard::class)->name('index');
        Route::get('/orders', \App\Livewire\Customer\OrderList::class)->name('orders');
        Route::get('/orders/{order:uuid}', \App\Livewire\Customer\OrderDetail::class)->name('orders.show');
        Route::get('/profile', \App\Livewire\Customer\ProfileEdit::class)->name('profile');

        // New Features
        Route::get('/saved-cars', \App\Livewire\Customer\SavedCars::class)->name('saved-cars');
        Route::get('/invoices', \App\Livewire\Customer\Invoices::class)->name('invoices');
        Route::get('/support', \App\Livewire\Customer\SupportTickets::class)->name('support');
        Route::get('/support/{uuid}', \App\Livewire\Customer\SupportTicketDetail::class)->name('support.show');
        Route::get('/notifications', \App\Livewire\Customer\NotificationsHub::class)->name('notifications');
        Route::get('/reviews', \App\Livewire\Customer\Reviews::class)->name('reviews');
    });
});

// KYC document previews — only reachable via a signed, short-lived URL generated
// by the admin Customer detail page. Never link the storage path directly.
Route::get('/admin/kyc-documents/{user:uuid}/{type}', [\App\Http\Controllers\Admin\KycDocumentController::class, 'show'])
    ->middleware(['signed', 'auth'])
    ->name('admin.kyc-documents.show');

require __DIR__.'/settings.php';
