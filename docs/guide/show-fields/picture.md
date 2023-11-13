# Picture

Class: `Code16\Sharp\Show\Fields\SharpShowPictureField`

## Configuration

The picture field has no configuration.

## Transformer

You must value this field with an URL of the image.

If you are using [Sharp built-in Upoad solution](../sharp-uploads.md), be sure to use the  `SharpUploadModelThumbnailUrlTransformer`:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer(
            'picture', 
            new SharpUploadModelThumbnailUrlTransformer(600)
        )
        ->transform([...]);
}
```

See [related documentation of this transformer here](../how-to-transform-data.md#SharpUploadModelThumbnailUrlTransformer).