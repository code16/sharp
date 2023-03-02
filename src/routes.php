<?php

use Code16\Sharp\Http\Api\Commands\DashboardCommandController;
use Code16\Sharp\Http\Api\Commands\EntityListEntityCommandController;
use Code16\Sharp\Http\Api\Commands\EntityListInstanceCommandController;
use Code16\Sharp\Http\Api\Commands\EntityListInstanceStateController;
use Code16\Sharp\Http\Api\Commands\ShowInstanceCommandController;
use Code16\Sharp\Http\Api\Commands\ShowInstanceStateController;
use Code16\Sharp\Http\Api\DownloadController;
use Code16\Sharp\Http\Api\Embeds\EmbedsController;
use Code16\Sharp\Http\Api\Embeds\EmbedsFormController;
use Code16\Sharp\Http\Api\EntityListController as ApiEntityListController;
use Code16\Sharp\Http\Api\FilesController;
use Code16\Sharp\Http\Api\FormController as ApiFormController;
use Code16\Sharp\Http\Api\FormUploadController;
use Code16\Sharp\Http\Api\GlobalFilterController;
use Code16\Sharp\Http\DashboardController;
use Code16\Sharp\Http\EntityListController;
use Code16\Sharp\Http\FormController;
use Code16\Sharp\Http\HomeController;
use Code16\Sharp\Http\LangController;
use Code16\Sharp\Http\LoginController;
use Code16\Sharp\Http\ShowController;
use Code16\Sharp\Http\SingleShowController;
use Code16\Sharp\Http\WebDispatchController;
use Illuminate\Support\Facades\Route;

// API routes
Route::group([
    'prefix' => '/'.sharp_base_url_segment().'/api',
    'middleware' => ['sharp_web', 'sharp_api_errors', 'sharp_api_validation', 'sharp_locale'],
], function () {
    Route::get('/dashboard/{dashboardKey}', [\Code16\Sharp\Http\Api\DashboardController::class, 'show'])
        ->name('code16.sharp.api.dashboard')
        ->middleware(['sharp_api_append_breadcrumb']);

    Route::get('/dashboard/{dashboardKey}/command/{commandKey}/form', [DashboardCommandController::class, 'show'])
        ->name('code16.sharp.api.dashboard.command.form');

    Route::post('/dashboard/{dashboardKey}/command/{commandKey}', [DashboardCommandController::class, 'update'])
        ->name('code16.sharp.api.dashboard.command');

//    Route::get('/list/{entityKey}', [ApiEntityListController::class, 'show'])
//        ->name('code16.sharp.api.list')
//        ->middleware(['sharp_api_append_list_authorizations', 'sharp_api_append_multiform_in_list', 'sharp_api_append_notifications', 'sharp_api_append_breadcrumb']);

    Route::post('/list/{entityKey}/reorder', [ApiEntityListController::class, 'update'])
        ->name('code16.sharp.api.list.reorder');

    Route::post('/list/{entityKey}/state/{instanceId}', [EntityListInstanceStateController::class, 'update'])
        ->name('code16.sharp.api.list.state');

    Route::post('/list/{entityKey}/command/{commandKey}', [EntityListEntityCommandController::class, 'update'])
        ->name('code16.sharp.api.list.command.entity');

    Route::get('/list/{entityKey}/command/{commandKey}/form', [EntityListEntityCommandController::class, 'show'])
        ->name('code16.sharp.api.list.command.entity.form');

    Route::post('/list/{entityKey}/command/{commandKey}/{instanceId}', [EntityListInstanceCommandController::class, 'update'])
        ->name('code16.sharp.api.list.command.instance');

    Route::get('/list/{entityKey}/command/{commandKey}/{instanceId}/form', [EntityListInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.list.command.instance.form');

//    Route::get('/show/{entityKey}/{instanceId?}', [ShowController::class, 'show'])
//        ->name('code16.sharp.api.show.show')
//        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_notifications', 'sharp_api_append_breadcrumb']);

    Route::post('/show/{entityKey}/command/{commandKey}/{instanceId?}', [ShowInstanceCommandController::class, 'update'])
        ->name('code16.sharp.api.show.command.instance');

    Route::get('/show/{entityKey}/command/{commandKey}/{instanceId}/form', [ShowInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.show.command.instance.form');

    // Specific route for single shows, because /show/{entityKey}/command/{commandKey}/{instanceId?}/data
    // does not work since instanceId is optional but not the last segment.
    Route::get('/show/{entityKey}/command/{commandKey}/form', [ShowInstanceCommandController::class, 'show'])
        ->name('code16.sharp.api.show.command.singleInstance.form');

    Route::post('/show/{entityKey}/state/{instanceId?}', [ShowInstanceStateController::class, 'update'])
        ->name('code16.sharp.api.show.state');

//    Route::get('/form/{entityKey}', [ApiFormController::class, 'create'])
//        ->name('code16.sharp.api.form.create')
//        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_breadcrumb']);

    Route::post('/form/{entityKey}', [ApiFormController::class, 'store'])
        ->name('code16.sharp.api.form.store');

    Route::get('/form/{entityKey}/{instanceId?}', [ApiFormController::class, 'edit'])
        ->name('code16.sharp.api.form.edit')
        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_breadcrumb']);

    Route::post('/form/{entityKey}/{instanceId?}', [ApiFormController::class, 'update'])
        ->name('code16.sharp.api.form.update');

    Route::delete('/form/{entityKey}/{instanceId?}', [ApiFormController::class, 'delete'])
        ->name('code16.sharp.api.form.delete');

    Route::get('/filters', [GlobalFilterController::class, 'index'])
        ->name('code16.sharp.api.filter.index');

    Route::post('/filters/{filterKey}', [GlobalFilterController::class, 'update'])
        ->name('code16.sharp.api.filter.update');

    Route::get('/download/{entityKey}/{instanceId?}', [DownloadController::class, 'show'])
        ->name('code16.sharp.api.download.show');

    Route::post('/files/{entityKey}/{instanceId?}', [FilesController::class, 'show'])
        ->name('code16.sharp.api.files.show');

    Route::post('/embeds/{embedKey}/{entityKey}', [EmbedsController::class, 'show'])
        ->name('code16.sharp.api.embed.show');

    Route::post('/embeds/{embedKey}/{entityKey}/form/init', [EmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/form', [EmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.form.update');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}', [EmbedsController::class, 'show'])
        ->name('code16.sharp.api.embed.instance.show');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form/init', [EmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.instance.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form', [EmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.instance.form.update');
});

// Web routes
Route::group([
    'prefix' => '/'.sharp_base_url_segment(),
    'middleware' => ['sharp_web', 'sharp_invalidate_cache'],
], function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('code16.sharp.login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('code16.sharp.login.post');

    Route::get('/', [HomeController::class, 'index'])
        ->name('code16.sharp.home');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('code16.sharp.logout');

    Route::get('/s-list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.list');

    Route::get('/s-show/{entityKey}', [SingleShowController::class, 'show'])
        ->name('code16.sharp.single-show');

    Route::get('/s-list/{entityKey}/{uri}s-show/{showEntityKey}/{instanceId}', [ShowController::class, 'show'])
        ->where('uri', '.*')
        ->name('code16.sharp.show');

    Route::get('/s-list/{entityKey}/{uri}s-form/{formEntityKey}', [FormController::class, 'show'])
        ->where('uri', '.*')
        ->name('code16.sharp.form');

    Route::get('/s-show/{entityKey}/{uri}', [WebDispatchController::class, 'index'])
        ->where('uri', '.*')
        ->name('code16.sharp.single-show.subpage');

    Route::get('/s-dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.dashboard');

    Route::post('/api/upload', [FormUploadController::class, 'store'])
        ->name('code16.sharp.api.form.upload');
});

// Localization
Route::get('/vendor/sharp/lang.js', [LangController::class, 'index'])
    ->name('code16.sharp.assets.lang');
