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