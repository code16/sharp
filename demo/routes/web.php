<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::passkeys();

Route::redirect('/', '/docs');

Route::get('/docs{segment}', function (?string $segment = null) {
    $segment = trim($segment ?: '', '/');

    if (! str_contains($segment, '/')) {
        $versions = json_decode(file_get_contents(base_path('../docs/versions/config.json')), true);
        if ($segment !== $versions[0]['slug']) {
            return redirect('/docs/'.$versions[0]['slug']);
        }
    }

    return match (true) {
        file_exists($html = public_path('/docs/'.$segment.'.html')) => response()->file($html),
        file_exists($html = public_path('/docs/'.$segment.'/index.html')) => response()->file($html),
        default => abort(404),
    };
})->where('segment', '.*');

Route::get('/post/{post}', function (Post $post) {
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
