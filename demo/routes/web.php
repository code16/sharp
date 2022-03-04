<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/docs/index.html');
});
