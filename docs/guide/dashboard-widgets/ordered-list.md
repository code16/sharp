# Ordered list widget

This widget intends to display data as an ordered list of items

```php
$widgetsContainer->addWidget(
    SharpOrderedListWidget::make('topTravelledShipTypes')
);
```

## Data valuation

Valuation is handled by a dedicated `$this->setOrderedListData(string $panelWidgetKey, array $data)` in the Dashboard class:

```php
function buildWidgetsData(): void
{
    $this->setOrderedListData(
        'topTravelledShipTypes', [
            [
                'label' => 'model EF5978',
                'count' => 89
            ],
            [
                'label' => 'model TT4448',
            ],
            [
                'label' => 'model EF5978',
                'count' => 17
            ],
            [
                'label' => 'model YY5557'
            ]
        ]
    );
}
```

Pass there the widget `key` and an array with the data as an array. Each item of the array should be an associative array. The key `label` is mandatory as it defines the ordered list item main content. 
You can also optionally define a count with key `count` associated with a number, it will show a badge with given value.

Here's a more realistic example with data fetched from a Model:

```php
$this->setOrderedListData(
    'topTravelledSpaceshipModels',
    SpaceshipType::orderBy('travel_count', 'desc')
        ->take(5)
        ->get()
        ->map(function(SpaceshipType $type) {
            return [
                'id' => $type->id,
                'label' => $type->label,
                'count' => $type->travel_count,
            ];
        })
        ->values()
        ->all()
);
``` 

## Item URL

You may want to add a link on each row. To do that, use the `buildItemLink()` method on the widget creation:

```php
$widgetsContainer->addWidget(
    SharpOrderedListWidget::make('topTravelledShipTypes')
            ->buildItemLink(function($item) {
                  return url('some-link');
            })
);
```

In order to make a link to a Sharp EntityList, Show or Form, this method can also return a [LinkTo instance](../link-to.md):

```php
$widgetsContainer->addWidget(
    SharpOrderedListWidget::make('topTravelledShipTypes')
            ->buildItemLink(function($item) {
                  return LinkToEntityList::make('spaceship')->addFilter('type', $item['id']); 
            })
);
```

As you can see, the link is built for each row, and is therefore data-dependant. 
In this example, we intend to link each row toward the "spaceship" Entity List, with the filter "type" set to the value of `$item['id']`. So with this data:

```php
function buildWidgetsData(): void
{
    $this->setOrderedListData(
        'topTravelledShipTypes', [
            [
                'id' => 12,
                'label' => 'model EF5978',
                'count' => 89,
            ]
        ]
    );
}
```

The item will be linked to `/sharp/s-list/spaceship?filter_type=12`. 
