<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/docs/index.html');
});

Route::get('/post/{post}', function (\App\Models\Post $post) {
    return view('pages.post', ['post' => $post]);
});

Route::get('/admin/users', function (Request $request) {
    $users = User::orderBy('name');

    foreach (explode(' ', trim($request->query('query'))) as $word) {
        $users->where(function (Builder $query) use ($word) {
            $query->orWhere('name', 'like', "%$word%")
                ->orWhere('email', 'like', "%$word%");
        });
    }

    return $users->limit(10)->get();
})->name('sharp.autocompletes.users.index');
