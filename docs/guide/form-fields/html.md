# Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration

### `setTemplate(string|View $template)`

Write the blade template as a string. Example:

```php
SharpFormHtmlField::make('panel')
    ->setTemplate('This product is offline since <strong>{{ $date }}</strong>')
```

This example would mean that your transformed data has an object named `panel` containing a `date` attribute. Here a custom transformer example for this particular case:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer('panel', fn ($value, $instance) => [
            'date' => $instance->deprecated_at->isoFormat()
        ])
        ->transform(Product::find($id));
}
```

You can also pass a view (blade) :

```php
SharpFormHtmlField::make('panel')
    ->setTemplate(view('sharp.form-htm-field'))
```

## Formatter

- `toFront`: sent as provided.
- `fromFront`: returns null (read-only).
