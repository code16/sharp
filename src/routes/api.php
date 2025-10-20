<?php

use Code16\Sharp\Http\Controllers\Api\ApiDashboardFiltersController;
use Code16\Sharp\Http\Controllers\Api\ApiEntityListController;
use Code16\Sharp\Http\Controllers\Api\ApiEntityListFiltersController;
use Code16\Sharp\Http\Controllers\Api\ApiFilterAutocompleteController;
use Code16\Sharp\Http\Controllers\Api\ApiFormAutocompleteController;
use Code16\Sharp\Http\Controllers\Api\ApiFormEditorUploadFormController;
use Code16\Sharp\Http\Controllers\Api\ApiFormRefreshController;
use Code16\Sharp\Http\Controllers\Api\ApiFormUploadController;
use Code16\Sharp\Http\Controllers\Api\ApiFormUploadThumbnailController;
use Code16\Sharp\Http\Controllers\Api\ApiSearchController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiDashboardCommandController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiEntityListEntityCommandController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiEntityListEntityStateController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiEntityListInstanceCommandController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiEntityListQuickCreationCommandController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiShowEntityStateController;
use Code16\Sharp\Http\Controllers\Api\Commands\ApiShowInstanceCommandController;
use Code16\Sharp\Http\Controllers\Api\Embeds\ApiEmbedsFormController;
use Code16\Sharp\Http\Controllers\DashboardController;
use Code16\Sharp\Http\Controllers\EntityListController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/'.sharp()->config()->get('custom_url_segment').'/api',
    'middleware' => ['sharp_common', 'sharp_api'],
], function () {
    Route::get('/dashboard/{dashboardKey}/command/{commandKey}/form', [ApiDashboardCommandController::class, 'show'])
        ->name('code16.sharp.api.dashboard.command.form');

    Route::post('/dashboard/{dashboardKey}/command/{commandKey}', [ApiDashboardCommandController::class, 'update'])
        ->name('code16.sharp.api.dashboard.command');

    Route::get('/list/{entityKey}/form/{formEntityKey}/create', [ApiEntityListQuickCreationCommandController::class, 'create'])
        ->name('code16.sharp.api.list.command.quick-creation-form.create');

    Route::post('/list/{entityKey}/form/{formEntityKey}/create', [ApiEntityListQuickCreationCommandController::class, 'store'])
        ->name('code16.sharp.api.list.command.quick-creation-form.store');

    // EmbeddedEntityLists
    Route::get('/list/{entityKey}', [EntityListController::class, 'show'])
        ->name('code16.sharp.api.list')
        ->middleware('cache.headers:no_store');

    Route::post('/list/{entityKey}/filters', [ApiEntityListFiltersController::class, 'store'])
        ->name('code16.sharp.api.list.filters.store');

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

    // EmbeddedDashboards
    Route::get('/dashboard/{dashboardKey}', [DashboardController::class, 'show'])
        ->name('code16.sharp.api.dashboard')
        ->middleware('cache.headers:no_store');

    Route::post('/dashboard/{dashboardKey}/filters', [ApiDashboardFiltersController::class, 'store'])
        ->name('code16.sharp.api.dashboard.filters.store');

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

    Route::post('/embeds/{embedKey}/{entityKey}/form/init', [ApiEmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/form', [ApiEmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.form.update');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form/init', [ApiEmbedsFormController::class, 'show'])
        ->name('code16.sharp.api.embed.instance.form.show');

    Route::post('/embeds/{embedKey}/{entityKey}/{instanceId}/form', [ApiEmbedsFormController::class, 'update'])
        ->name('code16.sharp.api.embed.instance.form.update');

    Route::post('/form/editors/upload/form/{entityKey}/{instanceId?}', [ApiFormEditorUploadFormController::class, 'update'])
        ->name('code16.sharp.api.form.editor.upload.form.update');

    Route::post('/upload/thumbnail/{entityKey}/{instanceId?}', [ApiFormUploadThumbnailController::class, 'show'])
        ->name('code16.sharp.api.form.upload.thumbnail.show');

    Route::post('/upload', [ApiFormUploadController::class, 'store'])
        ->name('code16.sharp.api.form.upload');

    Route::post('/form/autocomplete/{entityKey}/{autocompleteFieldKey}', [ApiFormAutocompleteController::class, 'index'])
        ->name('code16.sharp.api.form.autocomplete.index');

    Route::post('/form/refresh/{entityKey}', [ApiFormRefreshController::class, 'update'])
        ->name('code16.sharp.api.form.refresh.update');

    Route::post('/filters/autocomplete/{entityKey}/{filterKey}', [ApiFilterAutocompleteController::class, 'index'])
        ->name('code16.sharp.api.filters.autocomplete.index');
});
