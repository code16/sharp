<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Http\Requests\ImpersonateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $guardSuffix = config('sharp.auth.guard') ? ':'.config('sharp.auth.guard') : '';

        $this->middleware('sharp_guest'.$guardSuffix);
    }

    public function store(ImpersonateRequest $request): RedirectResponse
    {
        auth(config('sharp.auth.guard'))
            ->loginUsingId($request->input('user_id'));

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
