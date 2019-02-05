# Dashboard widget: Panel

This simple widget is based on the HTML Form Entity field, and intend to display any useful information to the user.

## Attributes (setters)

```php
    $this->addWidget(
        SharpPanelWidget::make("activeSpaceships")
            ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
            ->setLink('spaceship');
```

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

`<div v-if="show">result is {{value}}</div>`
`<div v-else>result is unknown</div>`



## Data valuation

Valuation is handled by a dedicated `$this->setPanelData(string $panelWidgetKey, array $data)` in the Dashboard class:

```php
    function buildWidgetsData()
    {
        $count = [...];
        
        $this->setPanelData(
            "activeSpaceships", ["count" => $count]
        );
    }
```

Simply pass there the widget `key` and an array with the data required by your template.