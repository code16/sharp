<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Code16\Sharp\Auth\Passkeys\PasskeyManager;
use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;

class SpatiePasskeyController extends Controller
{
    public function validate(): JsonResponse
    {
        $this->ensureCurrentUserCanRegisterPasskey();

        request()->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json([
            'passkeyOptions' => json_decode($this->generatePasskeyOptions()),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureCurrentUserCanRegisterPasskey();

        $passkey = request()->input('passkey');
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        $user = $this->currentUser();

        try {
            $storePasskeyAction->execute(
                $user,
                $passkey, $this->previouslyGeneratedPasskeyOptions(),
                request()->getHost(),
                ['name' => request()->input('name')]
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ])->errorBag('passkeyForm');
        }

        // Only flash the user id: the Passkey model can't be stored in a JSON-serialized session
        return redirect()
            ->route('code16.sharp.passkeys.registered')
            ->with('registered_passkey', $user->getAuthIdentifier());
    }

    /**
     * During the pending passkey MFA step, only a user without any passkey may enroll one
     * (first passkey setup); otherwise, the existing passkey must be used as second factor.
     */
    protected function ensureCurrentUserCanRegisterPasskey(): void
    {
        if (! app(MultiFactorManager::class)->isUsingPasskeyMethod()) {
            return;
        }

        $user = app(MultiFactorManager::class)->pendingUser();

        abort_if(! $user || app(PasskeyManager::class)->userHasPasskey($user), 403);
    }

    protected function currentUser(): Authenticatable&HasPasskeys
    {
        return app(MultiFactorManager::class)->isUsingPasskeyMethod()
            ? app(MultiFactorManager::class)->pendingUser()
            : auth()->user();
    }

    protected function generatePasskeyOptions(): string
    {
        $generatePassKeyOptionsAction = Config::getAction('generate_passkey_register_options', GeneratePasskeyRegisterOptionsAction::class);

        $options = $generatePassKeyOptionsAction->execute($this->currentUser());

        session()->put('passkey-registration-options', $options);

        return $options;
    }

    protected function previouslyGeneratedPasskeyOptions(): ?string
    {
        return session()->pull('passkey-registration-options');
    }
}
