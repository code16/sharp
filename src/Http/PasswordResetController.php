<?php

namespace Code16\Sharp\Http;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function create(): RedirectResponse|Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => request()->route('token'),
            'email' => request()->email,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', RulesPassword::defaults()],
        ]);

        $resetCallback = function ($user, $password) {
            $user
                ->forceFill([
                    config('sharp.auth.password_attribute', 'password') => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])
                ->save();
        };

        $passwordBroker = config('sharp.auth.forgotten_password.password_broker')
            ? app(config('sharp.auth.forgotten_password.password_broker'))
            : Password::broker(config('auth.defaults.passwords'));

        $status = $passwordBroker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            config('sharp.auth.forgotten_password.reset_password_callback') ?: $resetCallback
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('code16.sharp.login')
                ->with('status', __("sharp::$status"));
        }

        throw ValidationException::withMessages([
            'email' => [trans("sharp::$status")],
        ]);
    }
}
