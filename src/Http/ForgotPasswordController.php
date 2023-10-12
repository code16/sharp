<?php

namespace Code16\Sharp\Http;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    public function create(): RedirectResponse|Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $passwordBroker = config('sharp.auth.forgotten_password.password_broker')
            ? app(config('sharp.auth.forgotten_password.password_broker'))
            : Password::broker(config('auth.defaults.passwords'));

        $status = $passwordBroker->sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT || $status == Password::INVALID_USER) {
            return redirect()
                ->back()
                ->with('status', __("sharp::passwords.sent"));
        }

        throw ValidationException::withMessages([
            'email' => [trans("sharp::$status")],
        ]);
    }
}
