<?php

namespace Code16\Sharp\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => trans('sharp::auth.validation_error'),
            'password.required' => trans('sharp::auth.validation_error'),
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! $this->attemptToLogin()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('sharp::auth.invalid_credentials'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function attemptToLogin(): bool
    {
        $loginAttr = config('sharp.auth.login_attribute', 'email');
        $passwordAttr = config('sharp.auth.password_attribute', 'password');
        $shouldRemember = config('sharp.auth.suggest_remember_me', false) && $this->boolean('remember');

        return Auth::guard(config('sharp.auth.guard'))
            ->attempt(
                [$loginAttr => $this->input('login'), $passwordAttr => $this->input('password')],
                $shouldRemember,
            );
    }

    public function ensureIsNotRateLimited(): void
    {
        if (config('sharp.auth.rate_limiting.enabled', true) === false) {
            return;
        }

        if (! RateLimiter::tooManyAttempts($this->throttleKey(), config('sharp.auth.rate_limiting.max_attempts', 5))) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
