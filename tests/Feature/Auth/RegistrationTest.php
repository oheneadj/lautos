<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // I redirect to the KYC step rather than the dashboard so customers
    // provide Ghana Card / TIN details right after signing up (US-36).
    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('register.kyc', absolute: false));

    $this->assertAuthenticated();
});

test('repeated registration attempts from the same ip are throttled', function () {
    $attempt = fn (string $email) => $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => $email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $attempt("test{$i}@example.com")->assertSessionHasNoErrors();
        auth()->logout();
    }

    // The 6th attempt within the same minute, from the same IP, should be throttled.
    $attempt('test-overflow@example.com')->assertStatus(429);
});
