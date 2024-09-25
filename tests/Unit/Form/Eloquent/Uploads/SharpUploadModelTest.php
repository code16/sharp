<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Tests\Fixtures\Person;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
//    sharp()->config()->configureUploadsThumbnailCreation(
//        thumbnailsDisk: 'public',
//        thumbnailsDir: 'thumbnails',
//    );

    Storage::fake('local');
    Storage::fake('public');
});

it('fills several attributes at once when setting the magic file attribute', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    $upload->file = [
        'file_name' => 'test/test.png',
        'mime_type' => 'test_mime',
        'size' => 1,
    ];

    expect($upload->file_name)->toBe('test/test.png')
        ->and($upload->mime_type)->toBe('test_mime')
        ->and($upload->size)->toBe(1);
});

it('allows to create thumbnails', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    expect($upload->thumbnail(null, 150))
        ->toStartWith('/storage/thumbnails/data/-150_q-90/'.basename($file))
        ->and(Storage::disk('public')->exists('thumbnails/data/-150_q-90/'.basename($file)))
        ->toBeTrue();
});

it('returns null on error with a thumbnail creation', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    // Corrupt data
    $upload->update(['file_name' => null]);

    expect($upload->thumbnail(150))->toBeNull();
});

it('handles transformation filters when creating a thumbnail', function () {
    $filters = [
        'crop' => [
            'height' => .5,
            'width' => .75,
            'x' => .3,
            'y' => .34,
        ],
        'rotate' => [
            'angle' => 45,
        ],
    ];

    $upload = createSharpUploadModel(createImage());
    $upload->filters = $filters;
    $upload->save();

    $folderPath = 'thumbnails/data/-150_'.md5(serialize($filters)).'_q-90';

    expect($upload->thumbnail(null, 150))
        ->toStartWith("/storage/{$folderPath}/".basename($upload->file_name))
        ->and(Storage::disk('public')->exists("{$folderPath}/".basename($upload->file_name)))
        ->toBeTrue();
});

it('allows to call a closure after a thumbnail creation', function () {
    $thumbWasCreated = null;
    $thumbWasCreatedTwice = null;
    $file = createImage();
    $upload = createSharpUploadModel($file);

    $upload->thumbnail()
        ->setAfterClosure(function (bool $wasCreated, string $path, $disk) use (&$thumbWasCreated) {
            $thumbWasCreated = $wasCreated;
        })
        ->make(150);

    $upload->thumbnail()
        ->setAfterClosure(function (bool $wasCreated, string $path, $disk) use (&$thumbWasCreatedTwice) {
            $thumbWasCreatedTwice = $wasCreated;
        })
        ->make(150);

    expect($thumbWasCreated)->toBeTrue()
        ->and($thumbWasCreatedTwice)->toBeFalse();
});

it('allows to define an encoder when creating the thumbnail', function () {
    $file = createImage(name: 'my-image.png');
    $upload = createSharpUploadModel($file);

    $thumb = $upload->thumbnail()
        ->setQuality(80)
        ->when($upload->mime_type == 'image/png', fn ($thumbnail) => $thumbnail->toWebp())
        ->make(150);

    expect($thumb)
        ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.webp')
        ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.webp'))
        ->toBeTrue();
});

function createSharpUploadModel(string $file, ?object $model = null, ?string $modelKey = 'test'): SharpUploadModel
{
    return SharpUploadModel::create([
        'file_name' => $file,
        'size' => 120,
        'mime_type' => 'image/png',
        'disk' => 'local',
        'model_type' => $model ? get_class($model) : Person::class,
        'model_id' => $model ? $model->id : Person::create(['name' => 'Marie Curie'])->id,
        'model_key' => $modelKey,
    ]);
}
