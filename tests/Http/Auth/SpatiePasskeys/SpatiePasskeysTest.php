<?php


use Code16\Sharp\Auth\Passkeys\Commands\UpdatePasskeyNameCommand;
use Code16\Sharp\Auth\Passkeys\Entity\PasskeyEntity;
use Code16\Sharp\Auth\Passkeys\Entity\PasskeyList;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;
use Webauthn\PublicKeyCredentialCreationOptions;

use function Orchestra\Testbench\Pest\defineEnvironment;
use function Orchestra\Testbench\Pest\defineRoutes;


require_once __DIR__.'/helpers.php';

pest()->use(LazilyRefreshDatabase::class);

defineEnvironment(function () {
    sharp()->config()->enablePasskeys();
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

    sharp()->config()
        ->declareEntity(PersonEntity::class);
});

// --- PasskeyEntity tests ---

it('has correct prohibited actions', function () {
    $entity = new PasskeyEntity();
    $reflection = new \ReflectionProperty($entity, 'prohibitedActions');

    expect($reflection->getValue($entity))->toContain('create', 'update');
});

// --- PasskeyList data ---

it('returns list data for authenticated user', function () {
    $user = createPasskeyUser();
    createPasskey($user, ['name' => 'My Passkey', 'last_used_at' => now()->subDay()]);
    login($user);

    $list = app(PasskeyList::class);
    $data = $list->getListData();

    expect($data)->toHaveCount(1);
    expect($data[0]['name'])->toBe('My Passkey');
});

it('does not return passkeys of other users', function () {
    $user = createPasskeyUser();
    $otherUser = createPasskeyUser(['email' => 'other@example.org']);
    createPasskey($otherUser, ['name' => 'Other Passkey']);
    login($user);

    $list = app(PasskeyList::class);
    $data = $list->getListData();

    expect($data)->toHaveCount(0);
});

it('transforms last_used_at to human readable format', function () {
    $user = createPasskeyUser();
    createPasskey($user, ['last_used_at' => now()->subHour()]);
    login($user);

    $list = app(PasskeyList::class);
    $data = $list->getListData();

    expect($data[0]['last_used_at'])->toBeString()->not->toBeEmpty();
});

// --- PasskeyList delete ---

it('deletes a passkey for the authenticated user', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);
    login($user);

    $list = app(PasskeyList::class);
    $list->delete($passkey->id);

    expect($user->passkeys()->count())->toBe(0);
});

it('cannot delete another user passkey', function () {
    $user = createPasskeyUser();
    $otherUser = createPasskeyUser(['email' => 'other@example.org']);
    $passkey = createPasskey($otherUser);
    login($user);

    $list = app(PasskeyList::class);

    expect(fn () => $list->delete($passkey->id))
        ->toThrow(ModelNotFoundException::class);
});

// --- UpdatePasskeyNameCommand ---

it('renames a passkey', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);
    login($user);

    $command = app(UpdatePasskeyNameCommand::class);
    $command->execute($passkey->id, ['name' => 'Renamed Passkey']);

    expect($passkey->fresh()->name)->toBe('Renamed Passkey');
});

it('validates name is required when renaming', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);
    login($user);

    $command = app(UpdatePasskeyNameCommand::class);

    expect(fn () => $command->execute($passkey->id, ['name' => '']))
        ->toThrow(ValidationException::class);
});

it('authenticates user directly when logging in with passkey', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);

    loginUsingSpatiePasskey($passkey);

    $this->get(route('code16.sharp.passkeys.authenticated'))
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($user->id);
});

// --- PasskeyEventSubscriber ---

it('queues a cookie when passkey is used to authenticate', function () {
    // here we expects the SharpInternalServiceProvider is booted
    $user = createPasskeyUser();
    $passkey = createPasskey($user);

    loginUsingSpatiePasskey($passkey);

    $queued = Cookie::getQueuedCookies();
    $names = array_map(fn ($c) => $c->getName(), $queued);
    expect($names)->toContain('sharp_last_used_passkey');
});

// --- Usage badge ---

it('shows usage badge when passkey matches cookie', function () {
    $user = createPasskeyUser();
    $passkey = createPasskey($user);
    login($user);

    request()->cookies->set('sharp_last_used_passkey', (string) $passkey->id);

    $list = app(PasskeyList::class);
    $data = $list->getListData();

    expect($data[0]['usage'])->not->toBeNull();
});

it('does not show usage badge when passkey does not match cookie', function () {
    $user = createPasskeyUser();
    createPasskey($user);
    login($user);

    request()->cookies->set('sharp_last_used_passkey', '99999');

    $list = app(PasskeyList::class);
    $data = $list->getListData();

    expect($data[0]['usage'])->toBeNull();
});

// --- PasskeyController tests ---

it('renders the passkey create page', function () {
    login(createPasskeyUser());

    $this->get(route('code16.sharp.passkeys.create'))
        ->assertOk();
});

it('renders the passkey create page with prompt parameter', function () {
    login(createPasskeyUser());

    $this->get(route('code16.sharp.passkeys.create', ['prompt' => 1]))
        ->assertOk();
});

it('requires authentication to access passkey create', function () {
    $this->get(route('code16.sharp.passkeys.create'))
        ->assertRedirect();
});

it('validates name on passkey validate endpoint', function () {
    login(createPasskeyUser());

    $this->postJson(route('code16.sharp.passkeys.spatie.validate'), ['name' => ''])
        ->assertJsonValidationErrors('name');

    $this->postJson(route('code16.sharp.passkeys.spatie.validate'), ['name' => str_repeat('a', 256)])
        ->assertJsonValidationErrors('name');
});

it('returns passkey options on successful validate', function () {
    $user = createPasskeyUser();
    login($user);

    // Configure a fake action that returns a JSON string
    config()->set('passkeys.actions.generate_passkey_register_options', FakeGeneratePasskeyRegisterOptionsAction::class);

    $this->postJson(route('code16.sharp.passkeys.spatie.validate'), ['name' => 'My Key'])
        ->assertOk()
        ->assertJsonStructure(['passkeyOptions']);
});

it('store endpoint requires authentication', function () {
    $this->post(route('code16.sharp.passkeys.spatie.store'))
        ->assertRedirect();
});

it('store endpoint catches action errors and throws validation exception', function () {
    $user = createPasskeyUser();
    login($user);

    // Put fake options in session as the controller expects them
    session()->put('passkey-registration-options', '{"fake":"options"}');

    $this->postJson(route('code16.sharp.passkeys.spatie.store'), [
        'passkey' => 'invalid-passkey-data',
        'name' => 'My Key',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});


it('store endpoint calls StorePasskeyAction with appropriate arguments', function () {
    $user = createPasskeyUser();
    login($user);

    $passkeyData = '{"id":"some-id","rawId":"some-raw-id","type":"public-key","response":{"attestationObject":"some-attestation","clientDataJSON":"some-client-data"}}';
    $passkeyOptions = '{"challenge":"some-challenge"}';
    $passkeyName = 'My New Passkey';

    session()->put('passkey-registration-options', $passkeyOptions);

    $mockAction = \Mockery::mock(StorePasskeyAction::class);
    $mockAction->shouldReceive('execute')
        ->once()
        ->withArgs(function ($authenticatable, $passkeyJson, $optionsJson, $host, $additionalProperties) use ($user, $passkeyData, $passkeyOptions, $passkeyName) {
            return $authenticatable->is($user)
                && $passkeyJson === $passkeyData
                && $optionsJson === $passkeyOptions
                && $host === request()->getHost()
                && $additionalProperties === ['name' => $passkeyName];
        })
        ->andReturn(new TestPasskey());

    FakeStorePasskeyAction::$mock = $mockAction;
    config()->set('passkeys.actions.store_passkey', FakeStorePasskeyAction::class);

    $this->postJson(route('code16.sharp.passkeys.spatie.store'), [
        'passkey' => $passkeyData,
        'name' => $passkeyName,
    ])
        ->assertRedirect(route('code16.sharp.passkeys.registered'));

    $mockAction->shouldHaveReceived('execute');
});

// --- PasskeySkipPromptController tests ---

it('skip prompt redirects to home with cookie', function () {
    login(createPasskeyUser());

    $this->post(route('code16.sharp.passkeys.skip-prompt'))
        ->assertRedirect(route('code16.sharp.home'))
        ->assertCookie('sharp_skip_passkey_prompt');
});

// --- Fixtures ---

class FakeStorePasskeyAction extends StorePasskeyAction
{
    public static $mock;
    public function execute($authenticatable, $passkeyJson, $passkeyOptionsJson, $hostName, $additionalProperties = []): Passkey
    {
        return static::$mock->execute($authenticatable, $passkeyJson, $passkeyOptionsJson, $hostName, $additionalProperties);
    }
}

class FakeGeneratePasskeyRegisterOptionsAction extends GeneratePasskeyRegisterOptionsAction
{
    public function execute(HasPasskeys $authenticatable, bool $asJson = true): string|PublicKeyCredentialCreationOptions
    {
        return '{"challenge":"fake-challenge","rp":{"name":"test"}}';
    }
}

