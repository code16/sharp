<?php

namespace Code16\Sharp\Http;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    use ValidatesRequests;

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
        if(config('sharp.auth.login_page_url')) {
            return redirect()->to(config('sharp.auth.login_page_url'));
        }

        return view("sharp::login");
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
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
            return redirect()->intended('/' . sharp_base_url_segment());
        }

        return back()->with("invalid", true)->withInput();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $this->guard()->logout();

        return redirect()->to(
            config('sharp.auth.login_page_url', route("code16.sharp.login"))
        );
    }

    /**
     * @return string
     */
    protected function getSharpLoginAttribute()
    {
        return config("sharp.auth.login_attribute", "email");
    }

    /**
     * @return string
     */
    protected function getSharpPasswordAttribute()
    {
        return config("sharp.auth.password_attribute", "password");
    }

    /**
     * @return mixed
     */
    protected function guard()
    {
        return auth()->guard(config('sharp.auth.guard'));
    }
}