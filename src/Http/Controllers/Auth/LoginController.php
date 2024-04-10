<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthenticationNeeds2faException;
use Code16\Sharp\Http\Controllers\Auth\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct()
    {
        $guardSuffix = sharpConfig()->get('auth.guard') ? ':'.sharpConfig()->get('auth.guard') : '';
        $this->middleware('sharp_guest'.$guardSuffix)->only(['create', 'store']);
        $this->middleware('sharp_auth'.$guardSuffix)->only('destroy');
    }

    public function create(): RedirectResponse|Response
    {
        if ($loginPageUrl = sharpConfig()->get('auth.login_page_url')) {
            return redirect()->to($loginPageUrl);
        }

        return Inertia::render('Auth/Login', [
            'loginIsEmail' => sharpConfig()->get('auth.login_attribute') === 'email',
            'message' => sharpConfig()->get('auth.login_form_message'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (SharpAuthenticationNeeds2faException) {
            // Credentials are OK, the user is not yet authenticated, redirect to 2FA page
            return redirect()->route('code16.sharp.login.2fa');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('code16.sharp.home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(sharpConfig()->get('auth.guard'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($loginPageUrl = sharpConfig()->get('auth.login_page_url')) {
            return redirect()->to($loginPageUrl);
        }

        return redirect()->to(route('code16.sharp.home'));
    }
}
