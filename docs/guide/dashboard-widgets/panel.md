# Panel widget

This widget is based on the HTML Form field, and is intended to display any useful information to the user.

## Attributes (setters)

```php
$widgetsContainer->addWidget(
    SharpPanelWidget::make("activeSpaceships")
        ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
        ->setLink(LinkToEntityList::make("spaceship"));
```

Note that the `setLink()` method is expecting a [LinkTo... instance](../link-to.md).

The Panel needs a view template, that you can provide in two ways:

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

```vue
<div v-if="show">result is {{value}}</div>
<div v-else>result is unknown</div>
```

## Data valuation

Valuation is handled by a dedicated `$this->setPanelData(string $panelWidgetKey, array $data)` in the Dashboard class:

```php
function buildWidgetsData(): void
{
    $count = [...];

    $this->setPanelData(
        "activeSpaceships", ["count" => $count]
    );
}
```

Pass there the widget `key` and an array with the data required by your template.
