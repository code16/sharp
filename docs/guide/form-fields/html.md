# Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration


### `setInlineTemplate(string $template)`

::: v-pre
Just write the template as a string, using placeholders for data like this: `{{var}}` where "var" is some key to data sent to the front
:::



Example:

```php
SharpFormHtmlField::make("panel")
    ->setInlineTemplate(
        "<h1>{{count}}</h1> spaceships in activity"
    )
```

works when transformed data has been added for the html field:

```php
function find($id): array
    {
        return $this
            ->setCustomTransformer("panel", function($spaceship) {
                return [
                    "count" => $spaceship->activities->count()
                ];
            })
            ->transform(
                Spaceship::with("activities")->findOrFail($id)
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