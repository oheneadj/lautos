<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.index', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('visiting login with a same-site redirect_to stores it as the intended url', function () {
    $this->get(route('login', ['redirect_to' => '/cars/some-car?intent=order']));

    expect(session('url.intended'))->toBe('/cars/some-car?intent=order');
});

test('a malicious redirect_to is ignored', function () {
    $this->get(route('login', ['redirect_to' => 'https://evil.example.com']));
    expect(session('url.intended'))->toBeNull();

    $this->get(route('login', ['redirect_to' => '//evil.example.com']));
    expect(session('url.intended'))->toBeNull();
});

test('logging in after visiting login with a redirect_to resumes at that url instead of the dashboard', function () {
    $user = User::factory()->create();

    $this->get(route('login', ['redirect_to' => '/cars/some-car?intent=order']));

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/cars/some-car?intent=order');
    $this->assertAuthenticated();
});