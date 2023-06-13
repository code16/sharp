<?php

namespace Code16\Sharp\Http\Requests;

use Code16\Sharp\Auth\TwoFactor\Sharp2faService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login2faRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => trans('sharp::auth.2fa.validation_error'),
        ];
    }

    public function authenticate(Sharp2faService $sharp2faService): void
    {
        $this->ensureIsNotRateLimited();

        if (! $sharp2faService->checkCode($this->input('code'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => trans('sharp::auth.2fa.invalid'),
            ]);
        }

        $this->getGuard()->loginUsingId(
            $sharp2faService->userId(),
            $sharp2faService->remember(),
        );

        RateLimiter::clear($this->throttleKey());
        $sharp2faService->forgetCode();
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate($this->ip());
    }

    private function getGuard(): StatefulGuard
    {
        return Auth::guard(config('sharp.auth.guard'));
    }
}
