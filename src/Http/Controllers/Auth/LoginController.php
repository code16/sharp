<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Code16\Sharp\Auth\Passkeys\PasskeyManager;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationNeeds2faException;
use Code16\Sharp\Http\Controllers\Auth\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(PasskeyManager $passkeys): RedirectResponse|Response
    {
        if ($loginPageUrl = sharp()->config()->get('auth.login_page_url')) {
            return redirect()->to($loginPageUrl);
        }

        if ($passkeys->isEnabled()) {
            if (! Route::has('passkeys.login')) {
                throw new \Exception('Passkeys routes are not defined. Add `Route::passkeys()` in your routes/web.php file.');
            }
        }

        $message = sharp()->config()->get('auth.login_form_message');

        return Inertia::render('Auth/Login', [
            'loginIsEmail' => sharp()->config()->get('auth.login_attribute') === 'email',
            'message' => $message
                ? $message instanceof View
                    ? $message->render()
                    : view('sharp::partials.login-form-message', ['message' => $message])->render()
                : null,
            'passkeyError' => $passkeys->getErrorMessage(),
        ]);
    }

    public function store(LoginRequest $request, PasskeyManager $passkeys): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (SharpAuthenticationNeeds2faException) {
            // Credentials are OK, the user is not yet authenticated, redirect to 2FA page
            return redirect()->route('code16.sharp.login.2fa');
        }

        $request->session()->regenerate();

        if ($passkeys->isEnabled()
            && sharp()->config()->get('auth.passkeys.prompt_after_login')
            && ! $request->cookie('sharp_skip_passkey_prompt')
            && $request->boolean('supports_passkeys')
            && ! $passkeys->userHasPasskey($request->user())
        ) {
            return redirect()->route('code16.sharp.passkeys.create', ['prompt' => true]);
        }

        return redirect()->intended(route('code16.sharp.home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($logoutPageUrl = sharp()->config()->get('auth.logout_page_url')) {
            return redirect()->to($logoutPageUrl);
        }

        Auth::guard(sharp()->config()->get('auth.guard'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($loginPageUrl = sharp()->config()->get('auth.login_page_url')) {
            return redirect()->to($loginPageUrl);
        }

        return redirect()->to(route('code16.sharp.home'));
    }
}
