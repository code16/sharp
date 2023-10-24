<?php

use Code16\Sharp\Http\ImpersonateController;
use Code16\Sharp\Http\Login2faController;
use Code16\Sharp\Http\LoginController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp_base_url_segment(),
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

    Route::post('/impersonate', [ImpersonateController::class, 'store'])
        ->name('code16.sharp.impersonate');
});
