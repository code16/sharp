<?php

namespace Code16\Sharp\Http;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    use ValidatesRequests;

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("sharp::login");
    }

    public function store()
    {
        $this->validate(request(), [
            "login" => "required",
            "password" => "required",
        ]);

        $guard = auth()->guard($this->getSharpGuard());

        if($guard->attempt([
            $this->getSharpLoginAttribute() => request('login'),
            $this->getSharpPasswordAttribute() => request('password')
        ])) {
            $check = $this->getSharpAuthCheck();
            if(is_null($check) || $check->allowUserInSharp($guard->user())) {
                return redirect()->intended('/');
            }

            $guard->logout();
        }

        return back()->with("invalid", true)->withInput();
    }

    public function destroy()
    {
        auth()->guard($this->getSharpGuard())->logout();

        return redirect()->route("code16.sharp.login");
    }

    protected function getSharpGuard()
    {
        return config("sharp.auth.guard", config("auth.defaults.guard"));
    }

    protected function getSharpLoginAttribute()
    {
        return config("sharp.auth.login_attribute", "email");
    }

    protected function getSharpPasswordAttribute()
    {
        return config("sharp.auth.password_attribute", "password");
    }

    protected function getSharpAuthCheck()
    {
        return config("sharp.auth.check") ? app(config("sharp.auth.check")) : null;
    }
}