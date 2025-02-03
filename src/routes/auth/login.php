<?php

use Code16\Sharp\Http\Controllers\Auth\Login2faController;
use Code16\Sharp\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp()->config()->get('custom_url_segment'),
    'middleware' => ['sharp_common', 'sharp_web'],
], function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('code16.sharp.login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('code16.sharp.login.post');

    Route::get('/login/2fa', [Login2faController::class, 'create'])
        ->name('code16.sharp.login.2fa');

    Route::post('/login/2fa', [Login2faController::class, 'store'])
        ->name('code16.sharp.login.2fa.post');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('code16.sharp.logout');
});
