# Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration

### `setTemplate(string|View|Closure $template)`

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

Using a closure:

```php
SharpFormHtmlField::make('panel')
    ->setTemplate(function (array $data) {
        return 'You have chosen:'.$data['another_form_field'].'. Date: '.$data['date'];
    })
```

#### Accessing to other field values in the form

In the template, all other field values of the form are available (alongside the Html field value). This is particularly useful when using `setLiveRefresh()` (described below).

### `setLiveRefresh(bool $liveRefresh = true, ?array $linkedFields = null)`

Use this method to dynamically update Html field when the user changes another field. 
The `$linkedFields` parameter allows filtering which field to watch (without it the internal refresh endpoint is called on any field update).

```php
SharpFormHtmlField::make('total')
    ->setLiveRefresh(linkedFields: ['products'])
    ->setTemplate(function (array $data) {
        return 'Total:'.collect($data['products'])
            ->sum(fn ($product) => $product['price']);
    })
```

## Formatter

- `toFront`: sent as provided.
- `fromFront`: returns null (read-only).
