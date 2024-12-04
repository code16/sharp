<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/e2e/init', function () {
    Artisan::call('snapshot:load', [
        'name' => 'e2e-seed',
    ]);

    if(request()->input('login')) {
        $user = User::where('email', 'test@example.org')->firstOrFail();

        auth()->login($user);
    }

    return 'init ok';
});
