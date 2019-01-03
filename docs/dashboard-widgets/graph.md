# Dashboard widget: Graph

This widget intends to display a graph visualization of any data.

## Types

There are two graph types, and they share the exact same API. To choose one or the other, use its dedicated class:

### Line graph

```php
    $this->addWidget(
        SharpLineGraphWidget::make("capacities");
    );
```

### Bar graph

```php
    $this->addWidget(
        SharpBarGraphWidget::make("capacities");
    );
```

### Pie graph

```php
    $this->addWidget(
        SharpPieGraphWidget::make("capacities");
    );
```

## Attributes (setters)

### `setRatio(string $ratio)`

This attribute is used to define the graph ratio, which will be consistent in responsive mode. The expected format is `width:height`, so for instance `16:9` (the default) or `4:3`.


## Data valuation

Valuation is handled by a dedicated `$this->addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet)` in the Dashboard class:

```php
    $this->addGraphDataSet(
        "capacities",
        SharpGraphWidgetDataSet::make($values)
            ->setLabel("Capacities")
            ->setColor("blue")
    );
```

We use an instance of `Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet` to handle graph data. This object is built with a `$values` array which must contain numeric values.

`SharpGraphWidgetDataSet` defines two useful setters:

- `setLabel(string $label)` to set the legend label
- `setColor(string $color)`, where $color can be an HTML constant or an hexadecimal value, to set the data color.

You can chain call to `addGraphDataSet()` to add multiple data sets, with different colors and labels.
