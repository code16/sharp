# File

Class: `Code16\Sharp\Show\Fields\SharpShowFileField`

The purpose of this field is to present a downloadable file to the user.

## Configuration

### `setLabel()`

Set the field label.

## Transformer

Sharp expects an array with 3 keys:

```php
[
    'name' => '', // Relative file path
    'thumbnail' => '', // 1000px w * 400px h thumbnail full url
    'size' => x, // Size in bytes
]
```

If you are using Sharp solution for uploads, meaning the `SharpUploadModel` class [detailed here](../sharp-uploads.md), you can simply call the built-in transformer:

```php
$this->setCustomTransformer('file', new SharpUploadModelFormAttributeTransformer());
```

This transformer allows to act a bit on the thumbnail creation part, see its constructor for more details.