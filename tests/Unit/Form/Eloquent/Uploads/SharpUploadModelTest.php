<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\Thumbnail;
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

it('fills several attributes at once at save with the magic "file" attribute', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    $upload->file = [
        'file_name' => 'test/test.png',
        'mime_type' => 'test_mime',
        'size' => 1,
        'legend' => 'test_legend',
    ];

    $upload->save();

    expect($upload->file_name)->toBe('test/test.png')
        ->and($upload->mime_type)->toBe('test_mime')
        ->and($upload->size)->toBe(1)
        ->and($upload->custom_properties['legend'])->toBe('test_legend');
});

it('allows to create thumbnails', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    expect($upload->thumbnail(null, 150))
        ->toStartWith('/storage/thumbnails/data/-150_q-90/'.basename($file))
        ->and(Storage::disk('public')->exists('thumbnails/data/-150_q-90/'.basename($file)))
        ->toBeTrue();
});

it('allows to display thumbnails with no width or height params', function () {
    $file = createImage();
    $upload = createSharpUploadModel($file);

    expect($upload->thumbnail())
        ->toBeInstanceOf(Thumbnail::class)
        ->and((string) $upload->thumbnail())
        ->toStartWith('/storage/thumbnails/data/-_q-90/'.basename($file));
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

it('allows to define an encoder when creating the thumbnail', function (string $driver) {
    sharp()->config()
        ->configureUploadsThumbnailCreation(imageDriverClass: $driver);

    expect(
        createSharpUploadModel(createImage(name: 'my-image.png'))->thumbnail()->setQuality(80)
            ->toWebp()->make(150)
    )
        ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.webp')
        ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.webp'))->toBeTrue();

    expect(
        createSharpUploadModel(createImage(name: 'my-image.png'))->thumbnail()->setQuality(80)
            ->toGif()->make(150)
    )
        ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.gif')
        ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.gif'))->toBeTrue();

    expect(
        createSharpUploadModel(createImage(name: 'my-image.gif'))->thumbnail()->setQuality(80)
            ->toPng()->make(150)
    )
        ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.png')
        ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.png'))->toBeTrue();

    expect(
        createSharpUploadModel(createImage(name: 'my-image.gif'))->thumbnail()->setQuality(80)
            ->toJpeg()->make(150)
    )
        ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.jpeg')
        ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.jpeg'))->toBeTrue();

    if ($driver === \Intervention\Image\Drivers\Imagick\Driver::class) {
        expect(
            createSharpUploadModel(createImage(name: 'my-image.png'))->thumbnail()->setQuality(80)
                ->toAvif()->make(150)
        )
            ->toEqual('/storage/thumbnails/data/150-_q-80/my-image.avif')
            ->and(Storage::disk('public')->exists('thumbnails/data/150-_q-80/my-image.avif'))->toBeTrue();
    }
})
    ->with([
        \Intervention\Image\Drivers\Gd\Driver::class,
        \Intervention\Image\Drivers\Imagick\Driver::class,
    ]);

it('allows to create SVG thumbnail by only copying the file', function () {
    $file = createImage('local', 'test.svg');
    $upload = createSharpUploadModel($file);

    expect($upload->thumbnail(150, 150))
        ->toStartWith('/storage/thumbnails/data/test.svg')
        ->and(Storage::disk('public')->exists('thumbnails/data/test.svg'))
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
