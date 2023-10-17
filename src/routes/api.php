<?php

use Code16\Sharp\Http\Api\ApiEntityListController;
use Code16\Sharp\Http\Api\ApiFilesController;
use Code16\Sharp\Http\Api\ApiFormUploadController;
use Code16\Sharp\Http\Api\ApiSearchController;
use Code16\Sharp\Http\Api\Commands\ApiDashboardCommandController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListEntityCommandController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListEntityStateController;
use Code16\Sharp\Http\Api\Commands\ApiEntityListInstanceCommandController;
use Code16\Sharp\Http\Api\Commands\ApiShowEntityStateController;
use Code16\Sharp\Http\Api\Commands\ApiShowInstanceCommandController;
use Code16\Sharp\Http\Api\Embeds\ApiEmbedsController;
use Code16\Sharp\Http\Api\Embeds\ApiEmbedsFormController;
use Code16\Sharp\Http\EntityListController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp_base_url_segment().'/api',
    'middleware' => ['sharp_common', 'sharp_api'],
], function () {
    Route::get('/dashboard/{dashboardKey}/command/{commandKey}/form', [ApiDashboardCommandController::class, 'show'])
        ->name('code16.sharp.api.dashboard.command.form');

    Route::post('/dashboard/{dashboardKey}/command/{commandKey}', [ApiDashboardCommandController::class, 'update'])
        ->name('code16.sharp.api.dashboard.command');

    // EEL
    Route::get('/list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.api.list');

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

    Route::get('/search', [ApiSearchController::class, 'index'])
        ->name('code16.sharp.api.search.index');

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

    Route::post('/upload', [ApiFormUploadController::class, 'store'])
        ->name('code16.sharp.api.form.upload');
});