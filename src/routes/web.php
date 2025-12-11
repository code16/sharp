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
use Code16\Sharp\Http\Controllers\UpdateAssetsController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp()->config()->get('custom_url_segment'),
    'middleware' => ['sharp_common', 'sharp_web'],
], function () {
    // Redirect GET routes without filterKey
    Route::get('s-dashboard/{dashboardKey}', fn ($entityKey) => redirect(
        route('code16.sharp.dashboard', [
            'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
            'dashboardKey' => $entityKey,
        ])
    ));
    Route::get('s-list/{entityKey}', fn ($entityKey) => redirect(
        route('code16.sharp.list', [
            'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
            'entityKey' => $entityKey,
        ])
    ));
    Route::get('s-show/{entityKey}', fn ($entityKey) => redirect(
        route('code16.sharp.single-show', [
            'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
            'entityKey' => $entityKey,
        ])
    ));

    Route::get('/', [HomeController::class, 'index'])
        ->name('code16.sharp.home');

    Route::get('{filterKey}/s-dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.dashboard');

    Route::post('/s-dashboard/{dashboardKey}', [DashboardFiltersController::class, 'store'])
        ->name('code16.sharp.dashboard.filters.store');

    Route::get('{filterKey}/s-list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.list');

    Route::post('/s-list/{entityKey}/filters', [EntityListFiltersController::class, 'store'])
        ->name('code16.sharp.list.filters.store');

    Route::get('{filterKey}/s-show/{entityKey}', [SingleShowController::class, 'show'])
        ->name('code16.sharp.single-show');

    Route::get('download/{entityKey}/{instanceId?}', [DownloadController::class, 'show'])
        ->name('code16.sharp.download.show');

    Route::where([
        'parentUri' => '(s-list|s-show)/.+',
    ])->group(function () {
        // Redirect GET routes without filterKey
        Route::get('{parentUri}/s-show/{entityKey}/{instanceId}', fn ($parentUri, $entityKey, $instanceId) => redirect(
            route('code16.sharp.show.show', [
                'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'instanceId' => $instanceId,
            ])
        ));
        Route::get('{parentUri}/s-form/{entityKey}', fn ($parentUri, $entityKey) => redirect(
            route('code16.sharp.form.create', [
                'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
            ])
        ));
        Route::get('{parentUri}/s-form/{entityKey}/{instanceId}', fn ($parentUri, $entityKey, $instanceId) => redirect(
            route('code16.sharp.form.edit', [
                'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
                'parentUri' => $parentUri,
                'entityKey' => $entityKey,
                'instanceId' => $instanceId,
            ])
        ));

        Route::get('/{filterKey}/{parentUri}/s-show/{entityKey}/{instanceId}', [ShowController::class, 'show'])
            ->name('code16.sharp.show.show');

        Route::delete('/{parentUri}/s-show/{entityKey}/{instanceId}', [ShowController::class, 'delete'])
            ->name('code16.sharp.show.delete');

        Route::get('/{filterKey}/{parentUri}/s-form/{entityKey}', [FormController::class, 'create'])
            ->name('code16.sharp.form.create');

        Route::post('/{parentUri}/s-form/{entityKey}', [FormController::class, 'store'])
            ->name('code16.sharp.form.store');

        Route::get('/{filterKey}/{parentUri}/s-form/{entityKey}/{instanceId?}', [FormController::class, 'edit'])
            ->name('code16.sharp.form.edit');

        Route::post('/{parentUri}/s-form/{entityKey}/{instanceId?}', [FormController::class, 'update'])
            ->name('code16.sharp.form.update');
    });

    Route::post('/filters/{filterKey}', [GlobalFilterController::class, 'update'])
        ->name('code16.sharp.filters.update');

    Route::post('/update-assets', UpdateAssetsController::class)
        ->name('code16.sharp.update-assets');
});
