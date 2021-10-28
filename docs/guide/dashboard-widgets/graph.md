# Graph widget

This widget intends to display a graph visualization of any data.

## Types

There are three graph types, and they mostly share the same API. To choose one or the other, use its dedicated class:

### Line graph

```php
$widgetsContainer->addWidget(
    SharpLineGraphWidget::make("capacities")
);
```

### Bar graph

```php
$widgetsContainer->addWidget(
    SharpBarGraphWidget::make("capacities")
);
```

### Pie graph

```php
$widgetsContainer->addWidget(
    SharpPieGraphWidget::make("capacities")
);
```

## Attributes (setters)

### `setRatio(string $ratio)`

This attribute is used to define the graph ratio, which will be consistent in responsive mode. The expected format is `width:height`, so for instance `16:9` (the default) or `4:3`.

### `setHeight(int $height)`

Used to set an arbitrary height, in px; if set the ratio is ignored.

### `setShowLegend(bool $showLegend = true)`

Display or not the graph legend. Default is `true`.

### `setMinimal(bool $minimal = true)`

If true, legend and axis are hidden. Default is `false`.

### `setDisplayHorizontalAxisAsTimeline(bool $displayAsTimeline = true)`

**(Line and Bar)** If true, and if X axis values are valid dates, the graph will create a timeline repartition of dates, creating visual gaps between dates. Default is `false`. 

### `setCurvedLines(bool $curvedLines = true)`

**(Line only)** Display lines with curved angles. Default is `true`. 

### `setHorizontal(bool $horizontal = true)`

**(Bar only)** Display horizontal bars instead of vertical ones. Default is `false`.

## Data valuation

Valuation is handled by a dedicated method: `$this->addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet)`, in the Dashboard class:

```php
$this->addGraphDataSet(
    "capacities",
    SharpGraphWidgetDataSet::make($values)
        ->setLabel("Capacities")
        ->setColor("blue")
);
```

We use an instance of `Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet` to handle graph data. This object is built with a `$values` array which must contain numeric values, keyed by a label. Example:

```php
[
  "Pizza" => 4,
  "Hamburgers" => 12 
]
```

`SharpGraphWidgetDataSet` defines two useful setters:

- `setLabel(string $label)` to set the legend label
- `setColor(string $color)` where $color can be an HTML constant or an hexadecimal value, to set the data color.

You can chain calls to `addGraphDataSet()` to add multiple data sets, with different colors and labels.
