<?php

use App\Livewire\Customer\ProfileEdit;
use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ProfileEdit::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('address', '123 Test St')
        ->set('ghana_card_number', 'GHA-123456789-1')
        ->call('updateProfile');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ProfileEdit::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->set('address', '123 Test St')
        ->set('ghana_card_number', 'GHA-123456789-1')
        ->call('updateProfile');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    // I check trashed() rather than expecting fresh() to be null — User uses SoftDeletes,
    // so the row still exists with deleted_at set, and fresh() bypasses global scopes by
    // design (it always re-fetches the row regardless of the SoftDeletingScope).
    expect($user->fresh()->trashed())->toBeTrue();
    expect(User::find($user->id))->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});