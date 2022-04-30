<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/docs/index.html');
});

Route::get('/post/{post}', function (\App\Models\Post $post) {
    return view('sharp.post-preview', [
        'post' => $post,
    ]);
});
