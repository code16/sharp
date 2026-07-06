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
            if (! $request->user()->is($multiFactor->currentUser())) {
                Auth::guard(sharp()->config()->get('auth.guard'))->logout();

                return redirect()->route('code16.sharp.login.2fa')
                    ->withErrors(['error' => 'The passkey does not belong to the current user.']);
            }

            $multiFactor->currentHandler()->forgetCode();
        }

        return redirect(request()->input('intended_url', route('code16.sharp.home')));
    }
}
