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

        if(auth()->guard($this->getSharpGuard())->attempt([
            $this->getSharpLoginAttribute() => request('login'),
            $this->getSharpPasswordAttribute() => request('password')
        ])) {
            return redirect()->intended('/');
        }

        return back()->with("invalid", true)->withInput();
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
}