# File

Class: `Code16\Sharp\Show\Fields\SharpShowFileField`

The purpose of this field is to present a downloadable file to the user.

## Configuration

### `setLabel()`

Set the field label.

### `setStorageDisk(string $storageDisk)`

Declare the file storage disk (as configured in Laravel's `config/filesystem.php` config file).

### `setStorageBasePath(string $storageBasePath)`

Declare the destination base storage path. 

You can use the `{id}` special placeholder to declare the instance id in the path, which can be useful sometimes. For instance:
`$field->setStorageBasePath('/users/{id}/avatar')`

## Transformer

Sharp expects an array with 3 keys:

```php
[
    "name" => "", // Relative file path
    "thumbnail" => "", // 1000px w * 400px h thumbnail full url
    "size" => x, // Size in bytes
]
```

I you are using Sharp solution for uploads, meaning the `SharpUploadModel` class [detailed here](../sharp-uploads.md),
you can simply call the built-in transformer:

```php
$this->setCustomTransformer("file", new SharpUploadModelFormAttributeTransformer());
```

This transformer allows to act a bit n the thumbnail creation part, see its constructor for more details.