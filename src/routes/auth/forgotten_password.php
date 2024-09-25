<?php

use Code16\Sharp\Http\Controllers\Auth\ForgotPasswordController;
use Code16\Sharp\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp()->config()->get('custom_url_segment'),
    'middleware' => ['sharp_common', 'sharp_web', 'sharp_guest'],
], function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('code16.sharp.password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('code16.sharp.password.request.post');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'create'])
        ->name('code16.sharp.password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'store'])
        ->name('code16.sharp.password.reset.post');
});
