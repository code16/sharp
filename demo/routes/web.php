<?php

use App\Models\Post;
use App\Models\User;
use App\Support\DocVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::passkeys();

Route::redirect('/', '/docs');

Route::get('/docs{segment}', function (?string $segment = null) {
    $segment = trim($segment ?: '', '/');

    if (! str_contains($segment, '/')) {
        $latestSlug = DocVersion::latest()->slug;
        if ($segment !== $latestSlug) {
            return redirect('/docs/'.$latestSlug);
        }
    }

    return match (true) {
        file_exists($html = public_path('/docs/'.$segment.'.html')) => response()->file($html),
        file_exists($html = public_path('/docs/'.$segment.'/index.html')) => response()->file($html),
        default => abort(404),
    };
})->where('segment', '.*');

Route::get('/robots.txt', function () {
    $sitemaps = DocVersion::all()
        ->map(fn (DocVersion $version) => 'Sitemap: '.url('/docs/'.$version->slug.'/sitemap.xml'))
        ->implode("\n");

    return response("User-agent: *\nDisallow:\n\n".$sitemaps."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});

Route::get('/{file}', function (string $file) {
    $path = public_path('docs/'.DocVersion::latest()->slug.'/'.$file);

    return file_exists($path)
        ? response()->file($path, ['Content-Type' => 'text/plain; charset=UTF-8'])
        : abort(404);
})->whereIn('file', ['llms.txt', 'llms-full.txt']);

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
