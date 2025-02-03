<?php

use App\Http\Controllers\InitController;
use Illuminate\Support\Facades\Route;

Route::get('/e2e/init', InitController::class);
