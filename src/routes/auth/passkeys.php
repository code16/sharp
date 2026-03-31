<?php

use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeyController;
use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeySkipPromptController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp()->config()->get('custom_url_segment'),
    'middleware' => ['sharp_common', 'sharp_web', 'sharp_auth'],
], function () {
    Route::get('/passkeys/create', [PasskeyController::class, 'create'])
        ->name('code16.sharp.passkeys.create');

    Route::post('/passkeys/validate', [PasskeyController::class, 'validate'])
        ->name('code16.sharp.passkeys.validate');

    Route::post('/passkeys', [PasskeyController::class, 'store'])
        ->name('code16.sharp.passkeys.store');

    Route::post('/passkeys/skip-prompt', PasskeySkipPromptController::class)
        ->name('code16.sharp.passkeys.skip-prompt');
});
