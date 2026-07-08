<?php

namespace Code16\Sharp\Tests\Http\Auth;

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;

use function Orchestra\Testbench\Pest\defineEnvironment;
use function Orchestra\Testbench\Pest\defineRoutes;

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

    config()->set('auth.providers.users.model', PasskeyMultifactorTestUser::class);
    config()->set('passkeys.models.authenticatable', PasskeyMultifactorTestUser::class);
    config()->set('passkeys.models.passkey', TestPasskey::class);
});

function createMultifactorUser(array $attributes = []): PasskeyMultifactorTestUser
{
    return PasskeyMultifactorTestUser::create(array_merge([
        'email' => 'test@example.org',
        'name' => 'Test',
        'password' => bcrypt('password'),
    ], $attributes));
}

function createPasskey(PasskeyMultifactorTestUser $user, array $attributes = []): TestPasskey
{
    $id = DB::table('passkeys')->insertGetId(array_merge([
        'authenticatable_id' => $user->id,
        'name' => 'My Passkey',
        'credential_id' => 'test-credential-'.uniqid(),
        'data' => json_encode(['test' => true]),
        'last_used_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ], $attributes));

    return TestPasskey::find($id);
}

it('authenticates user directly when logging in with passkey', function () {
    $user = createMultifactorUser();
    $passkey = createPasskey($user);

    FakeFindPasskeyToAuthenticateAction::$passkey = $passkey;

    config()->set('passkeys.actions.find_passkey', FakeFindPasskeyToAuthenticateAction::class);
    session()->put('passkey-authentication-options', 'test');

    $this->post(route('passkeys.login'), ['start_authentication_response' => '{}'], headers: ['X-Sharp' => '1'])
        ->assertSessionMissing('authenticatePasskey::message')
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code16.sharp.passkeys.authenticated', [
            'intended_url' => route('code16.sharp.home'),
        ]));

    $this->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($user->id);
});

it('redirects to passkey creation when user has no passkey', function () {
    $user = createMultifactorUser();

    $this->post(
        route('code16.sharp.login.post'),
        ['login' => $user->email, 'password' => 'password']
    )
        ->assertRedirect(route('code16.sharp.login.2fa'));

    $this->get(route('code16.sharp.login.2fa'))
        ->assertRedirect(route('code16.sharp.passkeys.create'));

    expect(app(MultiFactorManager::class)->pendingUser()->id)->toBe($user->id);

    config()->set('passkeys.actions.store_passkey', FakeStorePasskeyAction::class);

    $this->postJson(route('code16.sharp.passkeys.spatie.store'))
        ->assertRedirect(route('code16.sharp.passkeys.registered'));

    $this->get(route('code16.sharp.passkeys.registered'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($user->id);
    expect(app(MultiFactorManager::class)->pendingUser())->toBeNull();
});

it('redirects to 2fa page when user has a passkey', function () {
    $user = createMultifactorUser();
    $passkey = createPasskey($user);

    $this->post(route('code16.sharp.login.post'), [
        'login' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('code16.sharp.login.2fa'));

    $this->get(route('code16.sharp.login.2fa'))
        ->assertOk();

    $request = AuthenticateUsingPasskeysRequest::create(route('passkeys.login'));
    $request->headers->set('X-Sharp', '1');
    event(new PasskeyUsedToAuthenticateEvent($passkey, $request));

    // Simulate entering passkey
    $this->actingAs($user, 'web');
    $this->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->check())->toBeTrue();
});

it('redirects to login with mismatch message when using another user passkey (test 4)', function () {
    $userA = createMultifactorUser(['email' => 'userA@example.org']);
    $userB = createMultifactorUser(['email' => 'userB@example.org']);
    createPasskey($userA);
    createPasskey($userB);

    // User A starts login
    $this->post(route('code16.sharp.login.post'), [
        'login' => $userA->email,
        'password' => 'password',
    ])->assertRedirect(route('code16.sharp.login.2fa'));

    // Simulate User B's passkey being used
    $this->actingAs($userB, 'web');

    $this->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.login'))
        ->assertSessionHas('status', __('sharp::auth.2fa.passkey.mismatch_error'));

    expect(auth()->check())->toBeFalse();
});

class FakeStorePasskeyAction extends StorePasskeyAction
{
    public function execute($authenticatable, $passkeyJson, $passkeyOptionsJson, $hostName, $additionalProperties = []): Passkey
    {
        return new TestPasskey();
    }
}

class FakeFindPasskeyToAuthenticateAction extends FindPasskeyToAuthenticateAction
{
    public static ?Passkey $passkey = null;

    public function execute(string $publicKeyCredentialJson, string $passkeyOptionsJson): ?Passkey
    {
        return self::$passkey;
    }
}

class TestPasskey extends Passkey
{
    protected $table = 'passkeys';
}

class PasskeyMultifactorTestUser extends User implements HasPasskeys
{
    use InteractsWithPasskeys;

    protected $table = 'users';

    public function getPasskeyName(): string
    {
        return $this->email;
    }
    public function getPasskeyId(): string
    {
        return (string) $this->id;
    }
    public function getPasskeyDisplayName(): string
    {
        return $this->name ?? $this->email;
    }
}
