<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Http\Requests\LoginRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $guardSuffix = config('sharp.auth.guard') ? ':'.config('sharp.auth.guard') : '';

        $this->middleware('sharp_guest'.$guardSuffix)
            ->only(['create', 'store']);

        $this->middleware('sharp_auth'.$guardSuffix)
            ->only('destroy');
    }

    public function create(): RedirectResponse|\Illuminate\Contracts\View\View
    {
        if ($loginPageUrl = value(config('sharp.auth.login_page_url'))) {
            return redirect()->to($loginPageUrl);
        }

        return view('sharp::login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended('/'.sharp_base_url_segment());
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
