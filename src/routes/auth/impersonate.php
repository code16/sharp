<?php

use Code16\Sharp\Http\ImpersonateController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp_base_url_segment(),
    'middleware' => ['sharp_common', 'sharp_web', 'sharp_guest'],
], function () {
    Route::get('/impersonate', [ImpersonateController::class, 'create'])
        ->name('code16.sharp.impersonate');

    Route::post('/impersonate', [ImpersonateController::class, 'store'])
        ->name('code16.sharp.impersonate.post');
});
