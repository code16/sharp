<?php

namespace Code16\Sharp\Http;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    use ValidatesRequests;

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $guardSuffix = config('sharp.auth.guard') ? ':' . config('sharp.auth.guard') : '';

        $this->middleware('sharp_guest' . $guardSuffix)
            ->only(['create','store']);

        $this->middleware('sharp_auth' . $guardSuffix)
            ->only('destroy');
    }

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

        if($this->guard()->attempt([
            $this->getSharpLoginAttribute() => request('login'),
            $this->getSharpPasswordAttribute() => request('password')
        ])) {
            return redirect()->intended('/sharp');
        }

        return back()->with("invalid", true)->withInput();
    }

    public function destroy()
    {
        $this->guard()->logout();

        return redirect()->route("code16.sharp.login");
    }

    protected function getSharpLoginAttribute()
    {
        return config("sharp.auth.login_attribute", "email");
    }

    protected function getSharpPasswordAttribute()
    {
        return config("sharp.auth.password_attribute", "password");
    }

    protected function guard()
    {
        return auth()->guard(config('sharp.auth.guard'));
    }
}