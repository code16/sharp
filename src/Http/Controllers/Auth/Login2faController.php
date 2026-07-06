<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Code16\Sharp\Auth\Passkeys\PasskeyManager;
use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Code16\Sharp\Enums\MultiFactorMethod;
use Code16\Sharp\Http\Controllers\Auth\Requests\Login2faRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class Login2faController extends Controller
{
    public function create(Request $request, MultiFactorManager $multiFactor, PasskeyManager $passkeys): RedirectResponse|Response
    {
        if ($multiFactor->currentHandler()?->isExpectingLogin()) {
            $user = $multiFactor->currentUser();

            if ($multiFactor->currentMethod() === MultiFactorMethod::Passkey
                && ! $passkeys->userHasPasskey($user)) {
                return redirect()->route('code16.sharp.passkeys.create');
            }

            return Inertia::render('Auth/Login2Fa', [
                'helpText' => $multiFactor->currentHandlerHelpText(),
                'mode' => $multiFactor->currentMethod(),
                'passkeyError' => $passkeys->getErrorMessage(),
            ]);
        }

        return redirect()->route('code16.sharp.login');
    }

    public function store(Login2faRequest $request, MultiFactorManager $twoFactor): RedirectResponse
    {
        $request->authenticate($twoFactor->currentHandler());
        $request->session()->regenerate();

        return redirect()->intended(route('code16.sharp.home'));
    }
}
