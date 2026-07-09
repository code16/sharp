<?php

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Passkey;

use function Orchestra\Testbench\Pest\defineEnvironment;
use function Orchestra\Testbench\Pest\defineRoutes;

require_once __DIR__.'/helpers.php';

pest()->use(LazilyRefreshDatabase::class);

defineEnvironment(function () {
    sharp()->config()
        ->enable2faByPasskey()
        ->declareEntity(PersonEntity::class);
});

defineRoutes(function () {
    Route::passkeys();
});

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name')->nullable();
        $table->string('email');
        $table->string('password')->nullable();
        $table->string('remember_token')->nullable();
        $table->timestamps();
    });

    Schema::create('passkeys', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('authenticatable_id');
        $table->text('name');
        $table->text('credential_id');
        $table->json('data');
        $table->timestamp('last_used_at')->nullable();
        $table->timestamps();
    });

    config()->set('auth.providers.users.model', PasskeyTestUser::class);
    config()->set('passkeys.models.authenticatable', PasskeyTestUser::class);
    config()->set('passkeys.models.passkey', TestPasskey::class);
});

it('authenticates user directly when logging in with passkey', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);

    loginUsingSpatiePasskey($passkey);

    test()->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->id())->toBe($user->id);
});

it('redirects to 2fa page when user has a passkey', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);

    $this->post(route('code16.sharp.login.post'), [
        'login' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('code16.sharp.login.2fa'));

    $this->get(route('code16.sharp.login.2fa'))
        ->assertOk();

    loginUsingSpatiePasskey($passkey);

    test()->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->id())->toBe($user->id);
});

it('redirects to login with mismatch message when using another user passkey (test 4)', function () {
    $userA = createPasskeyUser(['email' => 'userA@example.org']);
    $userB = createPasskeyUser(['email' => 'userB@example.org']);
    createPasskey($userA);
    $passkeyB = createPasskey($userB);

    // User A starts login
    $this->post(route('code16.sharp.login.post'), [
        'login' => $userA->email,
        'password' => 'password',
    ])->assertRedirect(route('code16.sharp.login.2fa'));

    loginUsingSpatiePasskey($passkeyB);

    test()->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.login'))
        ->assertSessionHas('status', __('sharp::auth.2fa.passkey.mismatch_error'));
});

it('redirects to passkey creation when user has no passkey', function () {
    $user = createPasskeyUser();

    $this->post(
        route('code16.sharp.login.post'),
        ['login' => $user->email, 'password' => 'password']
    )
        ->assertRedirect(route('code16.sharp.login.2fa'));

    $this->get(route('code16.sharp.login.2fa'))
        ->assertRedirect(route('code16.sharp.passkeys.create'));

    expect(app(MultiFactorManager::class)->pendingUser()->id)->toBe($user->id);

    config()->set('passkeys.actions.store_passkey', FakeStorePasskeyActionForMultiFactor::class);

    $this->postJson(route('code16.sharp.passkeys.spatie.store'))
        ->assertRedirect(route('code16.sharp.passkeys.registered'));

    $this->get(route('code16.sharp.passkeys.registered'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->id())->toBe($user->id);
    expect(app(MultiFactorManager::class)->pendingUser())->toBeNull();
});

class FakeStorePasskeyActionForMultiFactor extends StorePasskeyAction
{
    public function execute($authenticatable, $passkeyJson, $passkeyOptionsJson, $hostName, $additionalProperties = []): Passkey
    {
        return new TestPasskey();
    }
}
