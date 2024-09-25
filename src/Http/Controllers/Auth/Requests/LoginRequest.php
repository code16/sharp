<?php

namespace Code16\Sharp\Http\Controllers\Auth\Requests;

use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationNeeds2faException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\StatefulGuard;
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

        $remember = sharp()->config()->get('auth.suggest_remember_me') && $this->boolean('remember');

        if (!$this->attemptToLogin($remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('sharp::auth.invalid_credentials'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function attemptToLogin(bool $remember): bool
    {
        $guard = $this->getGuard();
        $credentials = [
            sharp()->config()->get('auth.login_attribute') => $this->input('login'),
            sharp()->config()->get('auth.password_attribute') => $this->input('password'),
        ];

        if (sharp()->config()->get('auth.2fa.enabled')) {
            // 2fa is globally configured, but we have to ensure that the user has 2fa enabled
            if ($guard->once($credentials)) {
                $handler = app(Sharp2faHandler::class);
                if ($handler->isEnabledFor($guard->user())) {
                    $handler->setUser($guard->user())->generateCode($remember);

                    throw new SharpAuthenticationNeeds2faException();
                }

                // 2fa is not enabled for this user, we can proceed with the login
            }
        }

        return $guard->attempt($credentials, $remember);
    }

    private function ensureIsNotRateLimited(): void
    {
        if (sharp()->config()->get('auth.rate_limiting.enabled') === false) {
            return;
        }

        if (!RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            sharp()->config()->get('auth.rate_limiting.max_attempts'))
        ) {
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

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }

    private function getGuard(): StatefulGuard
    {
        return Auth::guard(sharp()->config()->get('auth.guard'));
    }
}
