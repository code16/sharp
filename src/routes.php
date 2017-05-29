<?php

Route::group([
    'prefix' => '/sharp/api',
    'middleware' => ['sharp_context', 'sharp_errors'],
    'namespace' => 'Code16\Sharp\Http\Api'
], function() {

    Route::get("/form/{entityKey}")
        ->name("code16.sharp.api.form.create")
        ->uses('FormController@create');

    Route::get("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.edit")
        ->uses('FormController@edit');

    Route::post("/form/{entityKey}/{instanceId}")
        ->name("code16.sharp.api.form.update")
        ->uses('FormController@update');

    Route::post("/form/{entityKey}")
        ->name("code16.sharp.api.form.store")
        ->uses('FormController@store');

    Route::post('/upload')
        ->name("code16.sharp.api.form.upload")
        ->uses('FormUploadController@store');

});

Route::group([
    'prefix' => '/sharp',
    'namespace' => 'Code16\Sharp\Http'
], function() {

    Route::get('/form/{entityKey}/{instanceId}')
        ->name("code16.sharp.edit")
        ->uses('FrontFormController@edit');

    Route::get('/form/{entityKey}')
        ->name("code16.sharp.create")
        ->uses('FrontFormController@create');

});