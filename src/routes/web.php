<?php

use Code16\Sharp\Http\Api\DownloadController;
use Code16\Sharp\Http\DashboardController;
use Code16\Sharp\Http\EntityListController;
use Code16\Sharp\Http\FormController;
use Code16\Sharp\Http\GlobalFilterController;
use Code16\Sharp\Http\HomeController;
use Code16\Sharp\Http\ShowController;
use Code16\Sharp\Http\SingleShowController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp_base_url_segment(),
    'middleware' => ['sharp_common', 'sharp_web'],
], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('code16.sharp.home');

    Route::get('/s-dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.dashboard');

    Route::get('/s-list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.list');

    Route::get('/s-show/{entityKey}', [SingleShowController::class, 'show'])
        ->name('code16.sharp.single-show');

    Route::get('/download/{entityKey}/{instanceId?}', [DownloadController::class, 'show'])
        ->name('code16.sharp.download.show');

    Route::group([
        'prefix' => '/{uri}',
        'where' => ['uri' => '(s-list|s-show)/.*'],
    ], function () {
        Route::get('/s-show/{entityKey}/{instanceId}', [ShowController::class, 'show'])
            ->name('code16.sharp.show.show');

        Route::delete('/s-show/{entityKey}/{instanceId}', [ShowController::class, 'delete'])
            ->name('code16.sharp.show.delete');

        Route::get('/s-form/{entityKey}', [FormController::class, 'create'])
            ->name('code16.sharp.form.create');

        Route::post('/s-form/{entityKey}', [FormController::class, 'store'])
            ->name('code16.sharp.form.store');

        Route::get('/s-form/{entityKey}/{instanceId}', [FormController::class, 'edit'])
            ->name('code16.sharp.form.edit');

        Route::post('/s-form/{entityKey}/{instanceId}', [FormController::class, 'update'])
            ->name('code16.sharp.form.update');
    });

    Route::post('/filters/{filterKey}', [GlobalFilterController::class, 'update'])
        ->name('code16.sharp.filters.update');
});
