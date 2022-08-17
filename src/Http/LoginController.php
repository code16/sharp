<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

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

    public function create()
    {
        if (config('sharp.auth.login_page_url')) {
            return redirect()->to(config('sharp.auth.login_page_url'));
        }

        return view('sharp::login');
    }

    public function store()
    {
        $this->validate(request(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($this->attemptToLogin()) {
            return redirect()->intended('/'.sharp_base_url_segment());
        }

        return back()->with('invalid', true)->withInput();
    }

    public function destroy()
    {
        $this->guard()->logout();

        return redirect()->to(
            config('sharp.auth.login_page_url', route('code16.sharp.login')),
        );
    }

    protected function attemptToLogin(): bool
    {
        if ($guard = $this->guard()) {
            $loginAttr = config('sharp.auth.login_attribute', 'email');
            $passwordAttr = config('sharp.auth.password_attribute', 'password');
            $shouldRemember = config('sharp.auth.suggest_remember_me', false) && request()->boolean('remember');

            return $guard->attempt(
                [$loginAttr => request('login'), $passwordAttr => request('password')],
                $shouldRemember,
            );
        }

        throw new SharpInvalidConfigException('No auth guard was configured.');
    }

    protected function guard(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        return auth()->guard(config('sharp.auth.guard'));
    }
}
