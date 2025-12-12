# File

Class: `Code16\Sharp\Show\Fields\SharpShowFileField`

The purpose of this field is to present a downloadable file to the user.

## Configuration

### `setLabel()`

Set the field label.

## Transformer

Sharp expects an array formatted like this:

```php
[
    'name' => '', // Relative file path
    'path' => '', // Full file path
    'disk' => '', // Disk name
    'mime_type' => '', // Mime type
    'thumbnail' => '', // 1000px w * 400px h thumbnail full url
    'size' => x, // Size in bytes
]
```

If you are using Sharp’s solution for uploads, meaning the `SharpUploadModel` class [detailed here](../sharp-uploads.md), you can call the built-in transformer:

```php
$this->setCustomTransformer('file', new SharpUploadModelFormAttributeTransformer());
```

This transformer allows acting a bit on the thumbnail creation part, see its constructor for more details.

## A note on security

Sharp allows admins to download all uploaded files directly from the File field UI. However, this capability may introduce security concerns, since Sharp can access any file on the server (although this is largely mitigated by Flysystem, which is used under the hood). You can control this behavior by specifying a list of allowed disks in the configuration:

```php
class SharpServiceProvider extends SharpAppServiceProvider
{
    protected function configureSharp(SharpConfigBuilder $config): void
    {
        $config
            ->configureDownloads(
                allowedDisks: ['local', 'public'],
            )
            // [...]
    }
}
```
