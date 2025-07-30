<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/sharp');
});

Route::get('/sharp/remote-autocomplete', function () {
    $query = request('query');

    return collect(\App\Sharp\TestModels\TestModelForm::options())
        ->filter(function ($label, $id) use ($query) {
            return str_contains($label, $query);
        })
        ->map(function ($label, $id) {
            return ['id' => $id, 'label' => $label];
        })
        ->values();
})
    ->name('sharp.remote-autocomplete');

Route::get('/test', function () {
    return 'This is a test page';
})->name('test-page');
