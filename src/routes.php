<?php

use Code16\Sharp\Http\Api\Commands\ApiDashboardCommandController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListEntityCommandController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListInstanceCommandController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListEntityStateController;
use Code16\Sharp\Http\Api\Commands\ApiShowInstanceCommandController;
use Code16\Sharp\Http\Api\Commands\ApiShowEntityStateController;
use Code16\Sharp\Http\Api\DownloadController;
use Code16\Sharp\Http\Api\Embeds\ApiEmbedsController;
use Code16\Sharp\Http\Api\Embeds\ApiEmbedsFormController;
use Code16\Sharp\Http\Api\ApiEntityListController;
use Code16\Sharp\Http\Api\ApiFilesController;
use Code16\Sharp\Http\Api\ApiFormUploadController;
use Code16\Sharp\Http\Api\ApiSearchController;
use Code16\Sharp\Http\DashboardController;
use Code16\Sharp\Http\EntityListController;
use Code16\Sharp\Http\FormController;
use Code16\Sharp\Http\GlobalFilterController;
use Code16\Sharp\Http\HomeController;
use Code16\Sharp\Http\Login2faController;
use Code16\Sharp\Http\LoginController;
use Code16\Sharp\Http\ShowController;
use Code16\Sharp\Http\SingleShowController;
use Illuminate\Support\Facades\Route;

// API routes
Route::group([
    'prefix' => '/'.sharp_base_url_segment().'/api',
    'middleware' => ['sharp_common', 'sharp_api'],
], function () {
    Route::get('/dashboard/{dashboardKey}/command/{commandKey}/form', [ApiDashboardCommandController::class, 'show'])
        ->name('code16.sharp.api.dashboard.command.form');

    Route::post('/dashboard/{dashboardKey}/command/{commandKey}', [ApiDashboardCommandController::class, 'update'])
        ->name('code16.sharp.api.dashboard.command');

    Route::get('/list/{entityKey}', [ApiEntityListController::class, 'show'])
        ->name('code16.sharp.api.list')
        ->middleware(['sharp_api_append_list_authorizations', 'sharp_api_append_multiform_in_list']);

    Route::post('/list/{entityKey}/reorder', [ApiEntityListController::class, 'update'])
        ->name('code16.sharp.api.list.reorder');

    Route::delete('/list/{entityKey}/{instanceId}', [ApiEntityListController::class, 'delete'])
        ->name('code16.sharp.api.list.delete');

    Route::post('/list/{entityKey}/state/{instanceId}', [ApiEntityListEntityStateController::class, 'update'])
        ->name('code16.sharp.api.list.state');

    Route::post('/list/{entityKey}/command/{commandKey}', [ApiEntityListEntityCommandController::class, 'update'])
        ->name('code16.sharp.api.list.command.entity');

    Route::get('/list/{entityKey}/command/{commandKey}/form', [ApiEntityListEntityCommandController::class, 'show'])
        ->name('code16.sharp.api.list.command.entity.form');

    Route::post('/list/{entityKey}/command/{commandKey}/{instanceId}', [ApiEntityListInstanceCommandController::class, 'update'])
        ->name('code16.sharp.api.list.command.instance');

    Route::get('/list/{entityKey}/command/{commandKey}/{instanceId}/form', [ApiEntityListInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.list.command.instance.form');

    Route::post('/show/{entityKey}/command/{commandKey}/{instanceId?}', [ApiShowInstanceCommandController::class, 'update'])
        ->name('code16.sharp.api.show.command.instance');

    Route::get('/show/{entityKey}/command/{commandKey}/{instanceId}/form', [ApiShowInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.show.command.instance.form');

    // Specific route for single shows, because /show/{entityKey}/command/{commandKey}/{instanceId?}/data
    // does not work since instanceId is optional but not the last segment.
    Route::get('/show/{entityKey}/command/{commandKey}/form', [ApiShowInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.show.command.singleInstance.form');

    Route::post('/show/{entityKey}/state/{instanceId?}', [ApiShowEntityStateController::class, 'update'])
        ->name('code16.sharp.api.show.state');

    Route::delete('/show/{entityKey}/{instanceId}', [ShowController::class, 'delete'])
        ->name('code16.sharp.api.show.delete');

//    Route::get('/form/{entityKey}', [FormController::class, 'create'])
//        ->name('code16.sharp.api.form.create')
//        ->middleware(['sharp_api_append_instance_authorizations', 'sharp_api_append_breadcrumb']);

//    Route::post('/form/{entityKey}', [ApiFormController::class, 'store'])
//        ->name('code16.sharp.api.form.store');
//
//    Route::get('/form/{entityKey}/{instanceId?}', [ApiFormController::class, 'edit'])
//        ->name('code16.sharp.api.form.edit')
//        ->middleware(['sharp_api_append_instance_authorizations', 'sharp_api_append_breadcrumb']);
//
//    Route::post('/form/{entityKey}/{instanceId?}', [ApiFormController::class, 'update'])
//        ->name('code16.sharp.api.form.update');

//    Route::get('/filters', [ApiGlobalFilterController::class, 'index'])
//        ->name('code16.sharp.api.filter.index');
//
//    Route::post('/filters/{filterKey}', [ApiGlobalFilterController::class, 'update'])
//        ->name('code16.sharp.api.filter.update');

    Route::get('/search', [ApiSearchController::class, 'index'])
        ->name('code16.sharp.api.search.index');

    Route::get('/download/{entityKey}/{instanceId?}', [DownloadController::class, 'show'])
        ->name('code16.sharp.api.download.show');

    Route::post('/files/{entityKey}/{instanceId?}', [ApiFilesController::class, 'show'])
        ->name('code16.sharp.api.files.show');

    Route::post('/embeds/{embedKey}/{entityKey}', [ApiEmbedsController::class, 'show'])
        ->name('code16.sharp.api.embed.show');

    Route::post('/embeds/{embedKey}/{entityKey}/form/init', [ApiEmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/form', [ApiEmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.form.update');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}', [ApiEmbedsController::class, 'show'])
        ->name('code16.sharp.api.embed.instance.show');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form/init', [ApiEmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.instance.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form', [ApiEmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.instance.form.update');
});

// Web routes
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

    Route::get('/', [HomeController::class, 'index'])
        ->name('code16.sharp.home');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('code16.sharp.logout');

    Route::get('/s-dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.dashboard');

    Route::get('/s-list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.list');

    Route::get('/s-show/{entityKey}', [SingleShowController::class, 'show'])
        ->name('code16.sharp.single-show');

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

    Route::post('/api/upload', [ApiFormUploadController::class, 'store'])
        ->name('code16.sharp.api.form.upload');
});
