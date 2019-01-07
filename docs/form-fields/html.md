# Form field: Html

Class: `Code16\Sharp\Form\Fields\SharpFormHtmlField`

This field is read-only, and is meant to display some dynamic information in the form.

## Configuration


### `setInlineTemplate(string $template)`

Just write the template as a string, using placeholders for data like this: `{{var}}`.

Example:

```php
    $panel->setInlineTemplate(
        "<h1>{{count}}</h1> spaceships in activity"
    )
```

### `setTemplatePath(string $templatePath)`

Use this if you need more control: give the path of a full template, in its own file.

The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

`<div v-if="show">result is {{value}}</div>`
`<div v-else>result is unknown</div>`



## Formatter

- `toFront`: sent as provided.
- `fromFront`: returns null (read-only).