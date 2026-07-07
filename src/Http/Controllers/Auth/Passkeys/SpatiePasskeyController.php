<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

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
        request()->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json([
            'passkeyOptions' => json_decode($this->generatePasskeyOptions()),
        ]);
    }

    public function store(Request $request)
    {
        $passkey = request()->input('passkey');
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $registeredPasskey = $storePasskeyAction->execute(
                $this->currentUser(),
                $passkey, $this->previouslyGeneratedPasskeyOptions(),
                request()->getHost(),
                ['name' => request()->input('name')]
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ])->errorBag('passkeyForm');
        }

        return redirect()
            ->route('code16.sharp.passkeys.registered')
            ->with('registered_passkey', $registeredPasskey);
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
