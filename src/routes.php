<?php

Route::group([
    'prefix' => '/sharp/api',
    'middleware' => ['sharp_context', 'sharp_errors'],
    'namespace' => 'Code16\Sharp\Http\Api'
], function() {


    Route::get("/form/{key}/{id}")
        ->name("code16.sharp.api.form")
        ->uses('FormController@show');

    Route::post("/form/{key}/{id}")
        ->name("code16.sharp.api.form.update")
        ->uses('FormController@update');

    Route::post("/form/{key}")
        ->name("code16.sharp.api.form.store")
        ->uses('FormController@store');

});