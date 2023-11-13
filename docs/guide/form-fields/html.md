# Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration

### `setInlineTemplate(string $template)`

Write the template as a string, using placeholders for data like this: `{{var}}` where "var" is some key to data sent to the front

Example:

```php
SharpFormHtmlField::make('panel')
    ->setInlineTemplate('This product is offline since <strong>{{date}}</strong>')
```

Like other fields, this example would mean that your transformed data has an object named `panel` containing a `date` attribute. Here more than elsewhere you may need to use a custom transformer, like this is this piece of code:

```php
function find($id): array
    {
        return $this
            ->setCustomTransformer('panel', function($product) {
                return [
                    'date' => $product->deprecated_at->isoFormat()
                ];
            })
            ->transform(
                Product::findOrFail($id)
            );
    }
```

### `setTemplatePath(string $templatePath)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

```vue
<div v-if="show">result is {{value}}</div>
<div v-else>result is unknown</div>
```

## Formatter

- `toFront`: sent as provided.
- `fromFront`: returns null (read-only).
