<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sharp/remote-autocomplete', function () {
    return [];
})
    ->name('sharp.remote-autocomplete');
