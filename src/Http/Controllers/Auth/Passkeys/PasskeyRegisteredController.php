<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PasskeyRegisteredController extends Controller
{
    public function __invoke(Request $request, MultiFactorManager $multiFactor)
    {
        if (! session()->has('registered_passkey')) {
            return redirect()->route('code16.sharp.login');
        }

        if ($multiFactor->isUsingPasskeyMethod()) {
            if ($user = $multiFactor->pendingUser()) {
                Auth::guard(sharp()->config()->get('auth.guard'))->login($user);
                $request->session()->regenerate();
            }

            $multiFactor->currentHandler()->forgetCode();
        }

        return redirect()->intended(route('code16.sharp.home'));
    }
}
