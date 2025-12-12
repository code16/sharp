# Upload

Class: `Code16\Sharp\Form\Fields\SharpFormUploadField`

## General configuration

You can define the temp disk and directory where files will be stored until they are moved to the final folder, as well as a global max file size (which can be overriden by each field). Here are the default values:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureUploads(
                uploadDisk: 'local',
                uploadDirectory: 'tmp',
                globalMaxFileSize: 5,
                keepOriginalImageOnTransform: true
            )
            // [...]
    }
}
```

The fourth argument, `keepOriginalImageOnTransform`, is a boolean that defines if the original image should be kept when a transformation is applied on it (meaning that transformations are stored and applied on-the-fly: this is transparent when using Sharp’s [built-in way to handle uploads](../sharp-uploads.md). It can be overridden by each field (see below).

Sharp allows admins to download all uploaded files directly from the Upload field UI. However, this capability may introduce security concerns, since Sharp can access any file on the server (although this is largely mitigated by Flysystem, which is used under the hood). You can control this behavior by specifying a list of allowed disks in the configuration:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureDownloads(
                allowedDisks: ['local', 'public'],
            )
            // ...
    }
}
```

## Field Configuration

### `setStorageDisk(string $storageDisk)`

Set the destination storage disk (as configured in Laravel’s `config/filesystem.php` config file).

### `setStorageBasePath(string|Closure $storageBasePath)`

Set the destination base storage path.

::: warning
If you want to use a `{id}` special placeholder to add the instance id in the path (for instance: `$field->setStorageBasePath('/users/{id}/avatar')`), you must be the Eloquent case, leveraging `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater` (see [Eloquent form](../building-form#eloquent-case-where-the-magic-happens))
:::

### `setStorageTemporary()`

Keep the file only in the upload directory/disk (configured [here](#general-configuration)). `setStorageDisk()` and `setStorageBasePath()` will be ignored.

### `setAllowedExtensions(string|array $allowedExtensions)`

Define the allowed file extensions. 

For instance: `$field->setAllowedExtensions(['pdf', 'zip'])`

## Field Configuration in image case

### `setImageOnly(bool $imageOnly = true)`

When an upload field is configured to accept only images:
- the field will be forced to accept only images (allowed extensions set to "jpg, png, gif, svg, webp, bmp" by default),
- the uploaded file will be validated as an image (see below for more options),
- a thumbnail will be generated for the uploaded image.

### `setImageTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null)`

Allow the user to crop or rotate the visual, after the upload.  
The argument `$transformKeepOriginal` overrides the global config (which is `true` by default).

With `$transformKeepOriginal` set to true, the original file will remain unchanged, meaning the transformations will be stored apart: using the [built-in way to handle uploads](../sharp-uploads.md), it's transparent. Otherwise, see the Formatter part below.

### `setImageCropRatio(string $ratio, array $croppableFileTypes = null)`

Set a ratio constraint to uploaded images, formatted like this: `width:height`. For instance: `16:9`, or `1:1`.

When a crop ratio is set, any uploaded picture will be auto-cropped (centered).

The second argument, `$croppableFileTypes`, provide a way to limit the crop configuration to a list of image files extensions. For instance, it can be useful to define a crop for jpg and png, but not for gif because it will destroy animation.

### `setImageCompactThumbnail(bool $compactThumbnail = true)`

If true and if the upload has a thumbnail, it is limited to 60px high (to compact in a list item, for instance).

### `setImageOptimizeImage(bool $imageOptimize = true)`

If true, some optimization will be applied on the uploaded images (in order to reduce files weight). It relies on spatie's [image-optimizer](https://github.com/spatie/image-optimizer). Please note that you will need some of these packages on your system:
- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant 2](https://pngquant.org/)
- [SVGO](https://github.com/svg/svgo)
- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [cwebp](https://developers.google.com/speed/webp/docs/precompiled)

Check their documentation for [more instructions](https://github.com/spatie/image-optimizer#optimization-tools) on how to install.

## Validation

Notice that `setAllowedExtensions()` and `setImageOnly()` already are basic validation rules that Sharp will use both on the front-end and in the back-end.

But there are a few more rules available:

### `setMaxFileSize(int $maxFileSizeInMB)` and `setMinFileSize(int $minFileSizeInMB)`

Set the maximum and minimum (even if this is a rare use-case) file size in MB.

### `setImageDimensionConstraints(Illuminate\Validation\Rules\Dimensions $dimensions)`

Set image dimension constraints, leveraging the dedicated Laravel validation rule (see [the documentation](https://laravel.com/docs/validation#rule-dimensions)).

## Formatter

First, let's mention that Sharp provides an Eloquent built-in solution for uploads with the `SharpUploadModel` class, as [detailed here](../sharp-uploads.md), which greatly simplify the work (to be clear: it will handle everything from storage to image transformations).

Here's the documentation for the **not built-in solution**:

### `toFront`

The front expects an array with these keys:

```php
[
    'name' => '', // The file name
    'path' => '', // Relative file path
    'disk' => '', // Storage disk name
    'thumbnail' => '', // URL of the thumbnail (if image, obviously)
    'size' => x, // Size in bytes
    'filters' => [ // Transformations applied to the (image) file
        'crop' => [
            'x' => x,
            'y' => y,
            'width' => w,
            'height' => h,
        ],
        'rotate' => [
            'angle' => a,
        ]
    ]
]
```

The formatter can't handle it automatically, it is too project-specific. You'll have to provide this in a custom transformer ([see full documentation](../how-to-transform-data.md)) like this:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer('picture',
            function($value, $product, $attribute) {
                return [
                    'name' => basename($product->picture->name),
                    'path' => $product->picture->name,
                    'disk' => 's3',
                    'thumbnail' => /* thumbnail URL */,
                    'size' => $product->picture->size,
                    'filters' => $product->picture->filters
                ];
            }
        )
        ->transform(Product::find($id));
}
```

Do note that the thumbnail should comply to following rules: be at least 200x200 pixels, and more importantly it must apply the transformations defined by the filters if there is some. 

### `fromFront`

There are four cases:

#### newly uploaded file

The formatter must return an array like this:

```php
[
    'file_name' => '', // Target file path (relative)
    'size' => x, // File size in bytes
    'mime_type' => '', // File mime type
    'disk' => '', // Target storage disk name
    'filters' => [ // Transformations applied to the (image) file
        'crop' => [
            'x' => x,
            'y' => y,
            'width' => w,
            'height' => h,
        ],
        'rotate' => [
            'angle' => a,
        ]
    ]
];
```

It's up to you then to store any of these values in a DB or elsewhere.

Using the `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`, you will probably reach a solution like this:

```php
function update($id, array $data)
{
    $instance = $id ? Product::findOrFail($id) : new Product;

    $this->ignore('picture')->save($instance, $data);

    // Then handle $data['picture'] here
}
```

#### existing transformed image

In this case, the image was already handled in a previous post, and was then transformed (cropped, or rotated). The formatter will simply return and array with one `filters` key:

```php
[
    'filters' => [
        'crop' => [
            'x' => x,
            'y' => y,
            'width' => w,
            'height' => h,
        ],
        'rotate' => [
            'angle' => a,
        ]
    ]
];
```

Then you'll have to catch and store that if needed.

#### deleted file

The formatter will return `null` (note that the file **will not** be deleted from the storage).

#### existing and unchanged file

The formatter will return **an empty array**.

## Configure files jobs

Sharp handle files in jobs (copy / move and transformation). You can configure how these job should be dispatched:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureUploads(
                fileHandingQueue: 'default',
                fileHandlingQueueConnection: 'sync',
            )
            // [...]
    }
}
```

Queue and connection should be [properly configured](https://laravel.com/docs/queues).
