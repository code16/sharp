<?php

use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;

function loginUsingSpatiePasskey(Passkey $passkey): void
{
    FakeFindPasskeyToAuthenticateAction::$passkey = $passkey;

    config()->set('passkeys.actions.find_passkey', FakeFindPasskeyToAuthenticateAction::class);
    session()->put('passkey-authentication-options', 'test');

    test()->post(route('passkeys.login'), ['start_authentication_response' => '{}'], headers: ['X-Sharp' => '1'])
        ->assertSessionMissing('authenticatePasskey::message')
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('code16.sharp.passkeys.authenticated', [
            'intended_url' => route('code16.sharp.home'),
        ]));
}

function createPasskey(PasskeyTestUser $user, array $attributes = []): TestPasskey
{
    $id = DB::table('passkeys')->insertGetId([
        'authenticatable_id' => $user->id,
        'name' => 'My Passkey',
        'credential_id' => 'test-credential-'.uniqid(),
        'data' => json_encode(['test' => true]),
        'last_used_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
        ...$attributes,
    ]);

    return TestPasskey::find($id);
}

function createPasskeyUser(array $attributes = []): PasskeyTestUser
{
    return PasskeyTestUser::create(array_merge([
        'email' => 'test@example.org',
        'name' => 'Test',
        'password' => Hash::make('password'),
    ], $attributes));
}

class FakeFindPasskeyToAuthenticateAction extends FindPasskeyToAuthenticateAction
{
    public static ?Passkey $passkey = null;

    public function execute(string $publicKeyCredentialJson, string $passkeyOptionsJson): ?Passkey
    {
        return self::$passkey;
    }
}

class PasskeyTestUser extends User implements HasPasskeys
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

class TestPasskey extends Passkey
{
    protected $table = 'passkeys';

    public function data(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value,
            set: fn ($value) => ['data' => is_string($value) ? $value : json_encode($value)],
        );
    }
}
