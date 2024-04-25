<?php

use Code16\Sharp\Http\Controllers\Api\DownloadController;
use Code16\Sharp\Http\Controllers\DashboardController;
use Code16\Sharp\Http\Controllers\DashboardFiltersController;
use Code16\Sharp\Http\Controllers\EntityListController;
use Code16\Sharp\Http\Controllers\EntityListFiltersController;
use Code16\Sharp\Http\Controllers\FormController;
use Code16\Sharp\Http\Controllers\GlobalFilterController;
use Code16\Sharp\Http\Controllers\HomeController;
use Code16\Sharp\Http\Controllers\ShowController;
use Code16\Sharp\Http\Controllers\SingleShowController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharpConfig()->get('custom_url_segment'),
    'middleware' => ['sharp_common', 'sharp_web'],
], function () {
    Route::get('/', [HomeController::class, 'index'])
        ->name('code16.sharp.home');

    Route::get('/s-dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.dashboard');
    
    Route::post('/s-dashboard/{dashboardKey}', [DashboardFiltersController::class, 'store'])
        ->name('code16.sharp.dashboard.filters.store');

    Route::get('/s-list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.list');
    
    Route::post('/s-list/{entityKey}/filters', [EntityListFiltersController::class, 'store'])
        ->name('code16.sharp.list.filters.store');

    Route::get('/s-show/{entityKey}', [SingleShowController::class, 'show'])
        ->name('code16.sharp.single-show');

    Route::get('/download/{entityKey}/{instanceId?}', [DownloadController::class, 'show'])
        ->name('code16.sharp.download.show');

    Route::where([
        'parentUri' => '(s-list|s-show)/.+',
    ])->group(function () {
        Route::get('/{parentUri}/s-show/{entityKey}/{instanceId}', [ShowController::class, 'show'])
            ->name('code16.sharp.show.show');

        Route::delete('/{parentUri}/s-show/{entityKey}/{instanceId}', [ShowController::class, 'delete'])
            ->name('code16.sharp.show.delete');

        Route::get('/{parentUri}/s-form/{entityKey}', [FormController::class, 'create'])
            ->name('code16.sharp.form.create');

        Route::post('/{parentUri}/s-form/{entityKey}', [FormController::class, 'store'])
            ->name('code16.sharp.form.store');

        Route::get('/{parentUri}/s-form/{entityKey}/{instanceId}', [FormController::class, 'edit'])
            ->name('code16.sharp.form.edit');

        Route::post('/{parentUri}/s-form/{entityKey}/{instanceId}', [FormController::class, 'update'])
            ->name('code16.sharp.form.update');
    });

    Route::post('/filters/{filterKey}', [GlobalFilterController::class, 'update'])
        ->name('code16.sharp.filters.update');
});
