# List group widget

This widget intends to display data as an ordered list of items

```php
$this->addWidget(
    SharpListGroupWidget::make("topTravelledShipTypes");
);
```

## Data valuation

Valuation is handled by a dedicated `$this->setListGroupData(string $panelWidgetKey, array $data)` in the Dashboard class:

```php
function buildWidgetsData()
{
    $count = [...];

    $this->setListGroupData(
        "topTravelledShipTypes", [
            [
                "label" => "model EF5978",
                "link" => "/sharp/form/shiptype/12
                "count" => 89
            ],
            [
                "label" => "model TT4448",
                "link" => "/sharp/form/shiptype/24
            ],
            [
                "label" => "model EF5978",
                "count" => 17
            ],
            [
                "label" => "model YY5557"
            ]
        ]
    );
}
```

Pass there the widget `key` and an array with the data. Data should be an associative array. Each given key (as a string), will be displayed as a list group element. Each value should also be an associative array itself, which lets you, optionally define for each list item:
 - a link with key `link` associated with a valid url 
 - a count with key `count' associated with an integer