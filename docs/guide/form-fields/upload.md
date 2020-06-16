# Upload

Class: `Code16\Sharp\Form\Fields\SharpFormUploadField`

![Example](./upload.gif)

## General configuration

First, in order to get the upload part working, you have to define a "tmp" path where files will be stored until they are moved to the final folder. Here's the default:

```php
// in config/sharp.php

"uploads" => [
    "tmp_dir" => env("SHARP_UPLOADS_TMP_DIR", "tmp"),
]
```

This `tmp_dir` path is relative to the `local` filesystem defined in the Laravel configuration.


## Field Configuration

### `setMaxFileSize(float $sizeInMB)`

Max file size allowed.

### `setCropRatio(string $ratio, array $croppableFileTypes = null)`

Set a ratio constraint to uploaded images, formatted like this: `width:height`. For instance: `16:9`, or `1:1`.

When a crop ratio is set, any uploaded picture will be auto-cropped (centered).

The second argument, `$croppableFileTypes`, provide a way to limit the crop configuration to a list of image files extensions. For instance, it can be useful to define a crop for jpg and png, but not for gif because it will destroy animation.

### `setStorageDisk(string $storageDisk)`

Set the destination storage disk (as configured in Laravel's  `config/filesystem.php` config file).

### `setStorageBasePath(string $storageBasePath)`

Set the destination base storage path. 

You can use the `{id}` special placeholder to add the instance id in the path, which can be useful sometimes; **be sure to read the "Delayed creation" section, at the end of this page if you do.**

For instance:
`$field->setStorageBasePath('/users/{id}/avatar')`

### `setFileFilter($fileFilter)`

Set the allowed file extensions. You can pass either an array or a comma-separated list.

### `setFileFilterImages()`

Just a `setFileFilter([".jpg",".jpeg",".gif",".png"])` shorthand.

### `setCompactThumbnail($compactThumbnail = true)`

If true and if the upload has a thumbnail, it is limited to 60px high (to compact in a list item, for instance).

### `shouldOptimizeImage($shouldOptimize = true)`

If true, some optimization will be applied on the uploaded images (in order to reduce files weight). It relies on spatie's [image-optmizer](https://github.com/spatie/image-optimizer). Please note that you will need some of these packages on your system:
- [JpegOptim](http://freecode.com/projects/jpegoptim)
- [Optipng](http://optipng.sourceforge.net/)
- [Pngquant 2](https://pngquant.org/)
- [SVGO](https://github.com/svg/svgo)
- [Gifsicle](http://www.lcdf.org/gifsicle/)
- [cwebp](https://developers.google.com/speed/webp/docs/precompiled)

Check their documentation for [more instructions](https://github.com/spatie/image-optimizer#optimization-tools) on how to install.



## Formatter

This part is more complex for this field than others...

First, let's mention that Sharp provides an Eloquent built-in solution for uploads with the `SharpUploadModel` class, as [detailed here](../sharp-built-in-solution-for-uploads.md), which greatly simplify the work.

Here's the documentation for the non built-in solution:


### `toFront`

The front expects an array with three keys:

```php
[
    "name" => "", // Relative file path
    "thumbnail" => "", // 1000px w * 400px h thumbnail full url
    "size" => x, // Size in bytes
]
```

The formatter can't handle it automatically, it too project-specific. You'll have to provide this in a custom transformer ([see full documentation](../how-to-transform-data.md)) like this:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer("picture",
            function($value, $spaceship, $attribute) {
                return [
                    "name" => $spaceship->picture->name,
                    "thumbnail" => [...],
                    "size" => $spaceship->picture->size,
                ];
            }
        )
        ->transform(
            Spaceship::findOrFail($id)
        );
}
```

### `fromFront`

There are four cases:

#### newly uploaded file

The formatter will not perform any transformation, store the file on the configured location, and return an array like this:

```php
[
    "file_name" => "", // Relative file path
    "size" => x, // File size in bytes
    "mime_type" => "", // File mime type
    "disk" => "", // Storage disk name
    "transformed" => true // True if the file was transformed
];
```

It's up to you then to store any of these values in a DB or elsewhere.

Using the `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`, you will probably reach a solution like this:

```php
function update($id, array $data)
{
    $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

    $this->ignore("picture")->save($instance, $data);

    // Then handle $data["picture"] here
}
```

#### existing transformed file

In this case, the file was already stored, but was transformed (cropped, or rotated). The formatter will transform the file, store the result and simply return and array with one key:

```php
[
    "transformed" => true
];
```

Then you'll have to catch that if needed (to destroy all previous generated thumbnails for instance).


#### deleted file

The formatter will return null (note that the file **will not** be deleted from the storage).


#### existing and unchanged file

The formatter will return an empty array.

## Delayed creation

As described in the `setStorageBasePath()` section of this document, you can configure the file storage path with an `{id}` placeholder, meaning that `/users/{id}/avatar` will be converted (by the field formatter) in `/users/1/avatar` for instance. 

But in order to do this in a creation case, when there is no id yet, Sharp will need your instance to be stored first. To do so, the `update()` method of your Form will be called twice:

- one first time without any upload which needs the `{id}`,
- and one second time only with these fields, using the new id returned by `update()`.

This is usually OK, but in some cases this could lead to unexpected errors. Consider this code taken from Saturn (Sharp's demo project) where we handle Spaceships with a visual configured with an `{id}` placeholder in its path: 

```php
// in SpaceshipForm.php
function update($id, array $data)
{
    $instance = $id ? Spaceship::findOrFail($id) : new Spaceship();

    $this->save($instance, $data);

    if(($data["capacity"]) >= 1000) {
        $this->notify("this is a huge spaceship, by the way!");
    }

    return $instance->id;
}
```
Here we're using the `notify()` feature to display a message back to the user, and it's working, execpted in one case: on a Spaceship creation with a visual, Sharp will delay the upload handling and call this method twice. On the second pass (for the upload), PHP will crash on the `if(($data["capacity"]) >= 1000)` row because `$data["capacity"]` is not set (only the upload field would be set on this second pass). This has to be addressed, and a working solution could be to replace this line with:

```php
if(($data["capacity"] ?? 0) >= 1000) {
    [...]
}
```
