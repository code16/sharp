<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Code16\Sharp\Http\Controllers\Auth\Concerns\RedirectsToIntendedUrl;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PasskeyRegisteredController extends Controller
{
    use RedirectsToIntendedUrl;

    public function __invoke(Request $request, MultiFactorManager $multiFactor)
    {
        if (! session()->has('registered_passkey')) {
            return redirect()->route('code16.sharp.login');
        }

        if ($multiFactor->isUsingPasskeyMethod()) {
            $user = $multiFactor->pendingUser();

            // The registered passkey must belong to the pending user (who had no passkey before)
            abort_if(
                ! $user || session()->get('registered_passkey') !== $user->getAuthIdentifier(),
                403
            );

            Auth::guard(sharp()->config()->get('auth.guard'))->login($user);
            $request->session()->regenerate();

            $multiFactor->currentHandler()->forgetCode();
        }

        return $this->redirectToIntendedUrl();
    }
}
