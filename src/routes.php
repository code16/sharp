<?php

// API routes
Route::group([
    'prefix' => '/sharp/api',
    'middleware' => ['sharp_web', 'sharp_api_errors', 'sharp_auth', 'sharp_api_context'],
    'namespace' => 'Code16\Sharp\Http\Api'
], function() {

    Route::get("/dashboard")
        ->name("code16.sharp.api.dashboard")
        ->uses('DashboardController@index');

    Route::get("/list/{entityKey}")
        ->name("code16.sharp.api.list")
        ->middleware(['sharp_api_append_list_authorizations', 'sharp_save_list_params'])
        ->uses('EntityListController@show');

    Route::post("/list/{entityKey}/reorder")
        ->name("code16.sharp.api.list.reorder")
        ->uses('EntityListController@update');

    Route::post("/list/{entityKey}/state/{instanceId}")
        ->name("code16.sharp.api.list.state")
        ->uses('Commands\EntityStateController@update');

    Route::post("/list/{entityKey}/command/{commandKey}")
        ->name("code16.sharp.api.list.command.entity")
        ->uses('Commands\EntityCommandController@update');

    Route::post("/list/{entityKey}/command/{commandKey}/{instanceId}")
        ->name("code16.sharp.api.list.command.instance")
        ->uses('Commands\InstanceCommandController@update');

    Route::get("/form/{entityKey}")
        ->name("code16.sharp.api.form.create")
        ->middleware('sharp_api_append_form_authorizations')
        ->uses('FormController@create');

    Route::get("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.edit")
        ->middleware('sharp_api_append_form_authorizations')
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

});

// Web routes
Route::group([
    'prefix' => '/sharp',
    'middleware' => ['sharp_web'],
    'namespace' => 'Code16\Sharp\Http'
], function() {

    Route::group([
        'middleware' => ['sharp_guest'],
    ], function() {

        Route::get('/login')
            ->name("code16.sharp.login")
            ->uses('LoginController@create');

        Route::post('/login')
            ->name("code16.sharp.login.post")
            ->uses('LoginController@store');

    });

    Route::group([
        'middleware' => ['sharp_auth'],
    ], function() {

        Route::get('/')
            ->name("code16.sharp.dashboard")
            ->uses('DashboardController@index');

        Route::get('/logout')
            ->name("code16.sharp.logout")
            ->uses('LoginController@destroy');

        Route::get('/list/{entityKey}')
            ->name("code16.sharp.list")
            ->middleware('sharp_restore_list_params')
            ->uses('ListController@show');

        Route::get('/form/{entityKey}/{instanceId}')
            ->name("code16.sharp.edit")
            ->uses('FormController@edit');

        Route::get('/form/{entityKey}')
            ->name("code16.sharp.create")
            ->uses('FormController@create');

        Route::post('/api/upload')
            ->name("code16.sharp.api.form.upload")
            ->uses('Api\FormUploadController@store');
    });

});

// Localization
Route::get('/vendor/sharp/lang.js')
    ->name('code16.sharp.assets.lang')
    ->uses('Code16\Sharp\Http\LangController@index');