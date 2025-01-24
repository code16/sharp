<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/e2e/init', function () {
    Artisan::call('snapshot:load', [
        'name' => 'e2e-seed',
    ]);

    Storage::disk(sharp()->config()->get('uploads.tmp_disk'))
        ->deleteDirectory(sharp()->config()->get('uploads.tmp_dir'));

    Storage::disk(sharp()->config()->get('uploads.thumbnails_disk'))
        ->deleteDirectory(sharp()->config()->get('uploads.thumbnails_disk'));

    if (request()->input('login')) {
        $user = User::where('email', 'test@example.org')->firstOrFail();

        auth()->login($user);
    }

    return 'init ok';
});
