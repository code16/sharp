# Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration

### `setInlineTemplate(string $template)`

Write the template as a string, using placeholders for data (eg: `{{var}}`). Example:

```php
SharpFormHtmlField::make('panel')
    ->setInlineTemplate('This product is offline since <strong>{{date}}</strong>')
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

### `setTemplatePath(string $templatePath)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

```vue
<div v-if="show">result is {{value}}</div>
<div v-else>result is unknown</div>
```

### `setAdditionalTemplateData(array $data)`

Pass data to the template that is not part of the transformed data.

## Formatter

- `toFront`: sent as provided.
- `fromFront`: returns null (read-only).
