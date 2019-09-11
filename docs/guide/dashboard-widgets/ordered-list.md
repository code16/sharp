# Ordered list widget

This widget intends to display data as an ordered list of items

```php
$this->addWidget(
    SharpOrderedListWidget::make("topTravelledShipTypes");
);
```

## Data valuation

Valuation is handled by a dedicated `$this->setOrderedListData(string $panelWidgetKey, array $data)` in the Dashboard class:

```php
function buildWidgetsData()
{
    $count = [...];

    $this->setOrderedListData(
        "topTravelledShipTypes", [
            [
                "label" => "model EF5978",
                "url" => "/sharp/form/shiptype/12",
                "count" => 89
            ],
            [
                "label" => "model TT4448",
                "url" => "/sharp/form/shiptype/24",
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

Pass there the widget `key` and an array with the data as an array. Each array of the item should be an associative array. The key `label` is mandatory as it defines the ordered list item main content. You can also optionally define :
 - a link with key `url` associated with a valid url, the item then becomes clickable
 - a count with key `count` associated with a number, it will show a badge with given value
 
