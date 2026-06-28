<?php

use App\Livewire\Settings\Security;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Livewire\Livewire;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);
});

test('security settings page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertSee('Two-factor authentication')
        ->assertSee('Enable 2FA');
});

test('security settings page requires password confirmation when enabled', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('security.edit'));

    $response->assertRedirect(route('password.confirm'));
});

test('security settings page renders without two factor when feature is disabled', function () {
    config(['fortify.features' => []]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertSee('Update Password')
        ->assertDontSee('Two-factor authentication');
});

test('two factor authentication disabled when confirmation abandoned between requests', function () {
    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($user);

    $component = Livewire::test(Security::class);

    $component->assertSet('twoFactorEnabled', false);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
    ]);
});

test('password can be updated', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Security::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Security::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});

test('connecting google shows as connected and not connected otherwise', function () {
    $connected = User::factory()->create(['google_id' => 'google-1']);
    $this->actingAs($connected)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)->assertSet('googleConnected', true);

    $notConnected = User::factory()->create(['google_id' => null]);
    $this->actingAs($notConnected)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)->assertSet('googleConnected', false);
});

test('disconnecting google requires the correct password', function () {
    $user = User::factory()->create(['google_id' => 'google-1', 'password' => Hash::make('password')]);
    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)
        ->set('disconnect_google_password', 'wrong-password')
        ->call('disconnectGoogle')
        ->assertHasErrors(['disconnect_google_password']);

    $this->assertSame('google-1', $user->refresh()->google_id);

    Livewire::test(Security::class)
        ->set('disconnect_google_password', 'password')
        ->call('disconnectGoogle')
        ->assertHasNoErrors();

    $this->assertNull($user->refresh()->google_id);
});

test('a google only account without a password can request a password setup link', function () {
    Notification::fake();

    $user = User::factory()->create(['google_id' => 'google-1', 'has_password' => false]);
    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)
        ->assertSet('hasPassword', false)
        ->call('sendPasswordSetupLink');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('an account with a real password does not see the add password prompt', function () {
    $user = User::factory()->create(['has_password' => true]);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertDontSee('Add Password');
});

test('the active sessions list only shows the authenticated users own sessions', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    DB::table('sessions')->insert([
        ['id' => 'mine', 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'MyBrowser', 'payload' => 'x', 'last_activity' => time()],
        ['id' => 'someone-elses', 'user_id' => $other->id, 'ip_address' => '10.0.0.1', 'user_agent' => 'OtherBrowser', 'payload' => 'x', 'last_activity' => time()],
    ]);

    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

    $sessions = Livewire::test(Security::class)->get('sessions');

    expect(collect($sessions)->pluck('id')->all())->toContain('mine')->not->toContain('someone-elses');
});

test('logging out a session removes only that session', function () {
    $user = User::factory()->create();

    DB::table('sessions')->insert([
        ['id' => 'device-a', 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'A', 'payload' => 'x', 'last_activity' => time()],
        ['id' => 'device-b', 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'B', 'payload' => 'x', 'last_activity' => time()],
    ]);

    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)->call('logoutSession', 'device-a');

    $this->assertDatabaseMissing('sessions', ['id' => 'device-a']);
    $this->assertDatabaseHas('sessions', ['id' => 'device-b']);
});

test('a user cannot log out someone elses session by guessing its id', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    DB::table('sessions')->insert([
        ['id' => 'not-mine', 'user_id' => $other->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'A', 'payload' => 'x', 'last_activity' => time()],
    ]);

    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

    Livewire::test(Security::class)->call('logoutSession', 'not-mine');

    $this->assertDatabaseHas('sessions', ['id' => 'not-mine']);
});

test('logging out other sessions keeps only the current one', function () {
    $user = User::factory()->create();

    DB::table('sessions')->insert([
        ['id' => 'device-a', 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'A', 'payload' => 'x', 'last_activity' => time()],
        ['id' => 'device-b', 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'B', 'payload' => 'x', 'last_activity' => time()],
    ]);

    $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);
    $currentId = session()->getId();
    DB::table('sessions')->insert(['id' => $currentId, 'user_id' => $user->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'Current', 'payload' => 'x', 'last_activity' => time()]);

    Livewire::test(Security::class)->call('logoutOtherSessions');

    $this->assertDatabaseMissing('sessions', ['id' => 'device-a']);
    $this->assertDatabaseMissing('sessions', ['id' => 'device-b']);
    $this->assertDatabaseHas('sessions', ['id' => $currentId]);
});
