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

class LoginMultiFactorController extends Controller
{
    public function create(Request $request, MultiFactorManager $multiFactor, PasskeyManager $passkeys): RedirectResponse|Response
    {
        $user = $multiFactor->pendingUser();

        if ($user && $multiFactor->currentHandler()?->isExpectingLogin()) {
            if ($multiFactor->currentMethod() === MultiFactorMethod::Passkey
                && ! $passkeys->userHasPasskey($user)) {
                return redirect()->route('code16.sharp.passkeys.create');
            }

            return Inertia::render('Auth/LoginMultiFactor', [
                'helpText' => $multiFactor->currentHandlerHelpText(),
                'method' => $multiFactor->currentMethod(),
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
