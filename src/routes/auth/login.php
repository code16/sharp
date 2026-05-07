<?php

use Code16\Sharp\Http\Controllers\Auth\Login2faController;
use Code16\Sharp\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware(['sharp_common', 'sharp_web'])
    ->prefix('/'.sharp()->config()->get('custom_url_segment'))
    ->group(function () {
        Route::get('/login', [LoginController::class, 'create'])
            ->middleware('sharp_guest')
            ->name('code16.sharp.login');

        Route::post('/login', [LoginController::class, 'store'])
            ->middleware('sharp_guest')
            ->name('code16.sharp.login.post');

        Route::get('/login/2fa', [Login2faController::class, 'create'])
            ->name('code16.sharp.login.2fa');

        Route::post('/login/2fa', [Login2faController::class, 'store'])
            ->name('code16.sharp.login.2fa.post');

        Route::post('/logout', [LoginController::class, 'destroy'])
            ->middleware('sharp_auth')
            ->name('code16.sharp.logout');
    });
