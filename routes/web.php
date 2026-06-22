<?php

use App\Enums\CarStatus;
use App\Models\BlogPost;
use App\Models\Car;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Cars
Route::get('/cars', fn () => view('pages.cars.index'))->name('cars.index');

Route::get('/cars/{slug}', function (string $slug) {
    $car = Car::with(['make', 'carModel', 'carTrim', 'images'])
        ->where('slug', $slug)
        ->where('status', CarStatus::Available)
        ->firstOrFail();

    $carTitle = "{$car->year} {$car->make->name} {$car->carModel->name}";
    $carPrice = number_format($car->price_usd, 0);

    // I set per-car meta here so each listing is uniquely discoverable in search results.
    \Artesaos\SEOTools\Facades\SEOMeta::setTitle("{$carTitle} — \${$carPrice} | Livingston Autos");
    \Artesaos\SEOTools\Facades\SEOMeta::setDescription("Buy a {$carTitle} for \${$carPrice} (+ shipping), imported from {$car->country_of_origin} by Livingston Autos. {$car->mileage} km, {$car->transmission}, {$car->fuel_type}.");
    \Artesaos\SEOTools\Facades\OpenGraph::setTitle("{$carTitle} — \${$carPrice}");
    \Artesaos\SEOTools\Facades\OpenGraph::setDescription("Imported from {$car->country_of_origin} by Livingston Autos.");
    if ($car->images->first()) {
        \Artesaos\SEOTools\Facades\OpenGraph::addImage(\Illuminate\Support\Facades\Storage::url($car->images->first()->path));
    }

    return view('pages.cars.show', compact('car'));
})->name('cars.show');

// Blog
Route::get('/blog', fn () => view('pages.blog.index'))->name('blog.index');

Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::with(['category', 'author'])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

    return view('pages.blog.show', compact('post'));
})->name('blog.show');

// Contact
Route::view('/contact', 'pages.contact')->name('contact');

// Authenticated customer area
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// KYC document previews — only reachable via a signed, short-lived URL generated
// by the admin Customer detail page. Never link the storage path directly.
Route::get('/admin/kyc-documents/{user:uuid}/{type}', [\App\Http\Controllers\Admin\KycDocumentController::class, 'show'])
    ->middleware(['signed', 'auth'])
    ->name('admin.kyc-documents.show');

require __DIR__.'/settings.php';
