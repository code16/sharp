<?php

// API routes
Route::group([
    'prefix' => '/' . sharp_base_url_segment() . '/api',
    'middleware' => ['sharp_web', 'sharp_api_errors', 'sharp_api_context', 'sharp_api_validation', 'sharp_locale'],
    'namespace' => 'Code16\Sharp\Http\Api'
], function() {

    Route::get("/dashboard/{dashboardKey}")
        ->name("code16.sharp.api.dashboard")
        ->middleware(['sharp_api_append_breadcrumb'])
        ->uses('DashboardController@show');

    Route::get("/dashboard/{dashboardKey}/command/{commandKey}/data")
        ->name("code16.sharp.api.dashboard.command.data")
        ->uses('Commands\DashboardCommandController@show');

    Route::post("/dashboard/{dashboardKey}/command/{commandKey}")
        ->name("code16.sharp.api.dashboard.command")
        ->uses('Commands\DashboardCommandController@update');

    Route::get("/list/{entityKey}")
        ->name("code16.sharp.api.list")
        ->middleware(['sharp_api_append_list_authorizations', 'sharp_api_append_list_multiform', 'sharp_api_append_notifications', 'sharp_api_append_breadcrumb'])
        ->uses('EntityListController@show');

    Route::post("/list/{entityKey}/reorder")
        ->name("code16.sharp.api.list.reorder")
        ->uses('EntityListController@update');

    Route::post("/list/{entityKey}/state/{instanceId}")
        ->name("code16.sharp.api.list.state")
        ->uses('Commands\EntityListInstanceStateController@update');

    Route::post("/list/{entityKey}/command/{commandKey}")
        ->name("code16.sharp.api.list.command.entity")
        ->uses('Commands\EntityCommandController@update');

    Route::get("/list/{entityKey}/command/{commandKey}/data")
        ->name("code16.sharp.api.list.command.entity.data")
        ->uses('Commands\EntityCommandController@show');

    Route::post("/list/{entityKey}/command/{commandKey}/{instanceId}")
        ->name("code16.sharp.api.list.command.instance")
        ->uses('Commands\EntityListInstanceCommandController@update');

    Route::get("/list/{entityKey}/command/{commandKey}/{instanceId}/data")
        ->name("code16.sharp.api.list.command.instance.data")
        ->uses('Commands\EntityListInstanceCommandController@show');

    Route::get("/show/{entityKey}/{instanceId?}")
        ->name("code16.sharp.api.show.show")
        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_breadcrumb'])
        ->uses('ShowController@show');

    Route::post("/show/{entityKey}/command/{commandKey}/{instanceId?}")
        ->name("code16.sharp.api.show.command.instance")
        ->uses('Commands\ShowInstanceCommandController@update');

    Route::get("/show/{entityKey}/command/{commandKey}/{instanceId?}/data")
        ->name("code16.sharp.api.show.command.instance.data")
        ->uses('Commands\ShowInstanceCommandController@show');

    Route::post("/show/{entityKey}/state/{instanceId?}")
        ->name("code16.sharp.api.show.state")
        ->uses('Commands\ShowInstanceStateController@update');

    Route::get("/form/{entityKey}")
        ->name("code16.sharp.api.form.create")
        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_breadcrumb'])
        ->uses('FormController@create');

    Route::get("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.edit")
        ->middleware(['sharp_api_append_form_authorizations', 'sharp_api_append_breadcrumb'])
        ->uses('FormController@edit');

    Route::post("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.update")
        ->uses('FormController@update');

    Route::delete("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.delete")
        ->uses('FormController@delete');

    Route::post("/form/{entityKey}")
        ->name("code16.sharp.api.form.store")
        ->uses('FormController@store');

    Route::post("/download/{entityKey}/{instanceId}/{formUploadFieldKey}")
        ->name("code16.sharp.api.form.download")
        ->uses('FormDownloadController@show');

    Route::get("/filters")
        ->name("code16.sharp.api.filter.index")
        ->uses('GlobalFilterController@index');

    Route::post("/filters/{filterKey}")
        ->name("code16.sharp.api.filter.update")
        ->uses('GlobalFilterController@update');
});

// Web routes
Route::group([
    'prefix' => '/' . sharp_base_url_segment(),
    'middleware' => ['sharp_web'],
    'namespace' => 'Code16\Sharp\Http'
], function() {

    Route::get('/login')
        ->name("code16.sharp.login")
        ->uses('LoginController@create');

    Route::post('/login')
        ->name("code16.sharp.login.post")
        ->uses('LoginController@store');

    Route::get('/')
        ->name("code16.sharp.home")
        ->uses('HomeController@index');

    Route::get('/logout')
        ->name("code16.sharp.logout")
        ->uses('LoginController@destroy');

    Route::get('/list/{entityKey}')
        ->name("code16.sharp.list")
        ->middleware('sharp_store_breadcrumb')
        ->uses('ListController@show');

    Route::get('/show/{entityKey}/{instanceId?}')
        ->name("code16.sharp.show")
        ->middleware('sharp_store_breadcrumb')
        ->uses('ShowController@show');

    Route::get('/form/{entityKey}/{instanceId}')
        ->name("code16.sharp.edit")
        ->middleware('sharp_store_breadcrumb')
        ->uses('FormController@edit');

    Route::get('/form/{entityKey}')
        ->name("code16.sharp.create")
        ->middleware('sharp_store_breadcrumb')
        ->uses('FormController@create');

    Route::get('/dashboard/{dashboardKey}')
        ->name("code16.sharp.dashboard")
        ->middleware('sharp_store_breadcrumb')
        ->uses('DashboardController@show');

    Route::post('/api/upload')
        ->name("code16.sharp.api.form.upload")
        ->uses('Api\FormUploadController@store');
});

// Localization
Route::get('/vendor/sharp/lang.js')
    ->name('code16.sharp.assets.lang')
    ->uses('Code16\Sharp\Http\LangController@index');