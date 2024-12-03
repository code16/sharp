<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/e2e/seed', function () {
    Artisan::call('migrate:fresh', [
        '--seed' => true,
    ]);

    return 'seeded successfully';
});

Route::get('/e2e/login', function () {
    $user = User::where('email', 'test@example.org')->firstOrFail();

    auth()->login($user);

    return 'logged as '.$user->email;
});
