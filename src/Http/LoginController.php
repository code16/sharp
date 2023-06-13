<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Exceptions\Auth\SharpAuthenticationNeeds2faException;
use Code16\Sharp\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $guardSuffix = config('sharp.auth.guard') ? ':'.config('sharp.auth.guard') : '';

        $this->middleware('sharp_guest'.$guardSuffix)
            ->only(['create', 'store']);

        $this->middleware('sharp_auth'.$guardSuffix)
            ->only('destroy');
    }

    public function create(): RedirectResponse|View
    {
        if ($loginPageUrl = value(config('sharp.auth.login_page_url'))) {
            return redirect()->to($loginPageUrl);
        }

        return view('sharp::login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (SharpAuthenticationNeeds2faException $ex) {
            // Credentials are OK, the user is not yet authenticated, redirect to 2FA page
            return redirect()->route('code16.sharp.login.2fa');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('code16.sharp.home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(config('sharp.auth.guard'))->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($loginPageUrl = value(config('sharp.auth.login_page_url'))) {
            return redirect()->to($loginPageUrl);
        }

        return redirect()->to(route('code16.sharp.login'));
    }
}
