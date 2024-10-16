<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
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

        $passwordBroker = sharp()->config()->get('auth.forgotten_password.password_broker')
            ?: Password::broker(config('auth.defaults.passwords'));

        $status = $passwordBroker->sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT || $status == Password::INVALID_USER) {
            return redirect()
                ->back()
                ->with('status_title', __('sharp::passwords.sent_title'))
                ->with('status', __('sharp::passwords.sent'));
        }

        throw ValidationException::withMessages([
            'email' => [trans("sharp::$status")],
        ]);
    }
}
