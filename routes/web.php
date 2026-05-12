<?php

use App\Enums\CarStatus;
use App\Models\BlogPost;
use App\Models\Car;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Cars
Route::get('/cars', fn () => view('pages.cars.index'))->name('cars.index');

Route::get('/cars/{uuid}', function (string $uuid) {
    $car = Car::with(['make', 'carModel', 'carTrim', 'images'])
        ->where('uuid', $uuid)
        ->where('status', CarStatus::Available)
        ->firstOrFail();

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

require __DIR__.'/settings.php';
