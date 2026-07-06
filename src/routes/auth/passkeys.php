<?php

use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeyAuthenticatedController;
use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeyController;
use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeySkipPromptController;
use Code16\Sharp\Http\Controllers\Auth\Passkeys\SpatiePasskeyController;
use Code16\Sharp\Http\Middleware\SharpAuthenticateOrInMultiFactor;
use Illuminate\Support\Facades\Route;

Route::middleware(['sharp_common', 'sharp_web'])
    ->prefix('/'.sharp()->config()->get('custom_url_segment'))
    ->group(function () {
        Route::middleware([SharpAuthenticateOrInMultiFactor::class])->group(function () {
            Route::get('/passkeys/create', [PasskeyController::class, 'create'])
                ->name('code16.sharp.passkeys.create');

            Route::post('/spatie-passkeys/validate', [SpatiePasskeyController::class, 'validate'])
                ->name('code16.sharp.passkeys.spatie.validate');

            Route::post('/spatie-passkeys', [SpatiePasskeyController::class, 'store'])
                ->name('code16.sharp.passkeys.spatie.store');
        });

        Route::middleware(['sharp_auth'])->group(function () {
            Route::post('/passkeys/skip-prompt', PasskeySkipPromptController::class)
                ->name('code16.sharp.passkeys.skip-prompt');

            Route::get('/passkeys/authenticated', PasskeyAuthenticatedController::class)
                ->name('code16.sharp.passkeys.authenticated');
        });
    });
