<?php

namespace Code16\Sharp\Http\Requests;

use Code16\Sharp\Auth\TwoFactor\Sharp2faService;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationNeeds2faException;
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

        if (config('sharp.auth.2fa.enabled')) {
            // User is not yet authenticated
            app(Sharp2faService::class)->generateAndSendTokenFor($this->getGuard()->user());
//            $this->session()->put('sharp:2fa:user_id', $this->getGuard()->id());
//            $this->session()->put('sharp:2fa:user_id', $this->getGuard()->id());
            throw new SharpAuthenticationNeeds2faException();
        }
    }

    private function attemptToLogin(): bool
    {
        $guard = $this->getGuard();
        $credentials = [
            config('sharp.auth.login_attribute', 'email') => $this->input('login'),
            config('sharp.auth.password_attribute', 'password') => $this->input('password')
        ];
        
        if (config('sharp.auth.2fa.enabled')) {
            return $guard->once($credentials);
        }
        
        return $guard->attempt(
            $credentials,
            config('sharp.auth.suggest_remember_me', false) && $this->boolean('remember'),
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

    public function getGuard(): \Illuminate\Contracts\Auth\StatefulGuard
    {
        return Auth::guard(config('sharp.auth.guard'));
    }
}
