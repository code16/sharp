<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PasskeyAuthenticatedController extends Controller
{
    public function __invoke(Request $request, MultiFactorManager $multiFactor)
    {
        if ($multiFactor->isUsingPasskeyMethod()) {
            $authId = Auth::guard(sharp()->config()->get('auth.guard'))->user()->getAuthIdentifier();
            $pendingId = $multiFactor->pendingUser()?->getAuthIdentifier();

            if ($authId !== $pendingId) {
                Auth::guard(sharp()->config()->get('auth.guard'))->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('code16.sharp.login')
                    ->with('status', __('sharp::auth.2fa.passkey.mismatch_error'))
                    ->with('status_level', 'error');
            }

            $multiFactor->currentHandler()->forgetCode();
        }

        return redirect(request()->input('intended_url', route('code16.sharp.home')));
    }
}
