# Graph widget

This widget intends to display a graph visualization of any data. There are four graph types, and they mostly share the same API. To choose one or the other, use its dedicated class:

## Line

```php
$widgetsContainer->addWidget(
    SharpLineGraphWidget::make('sales')
);
```

Along with the [common configuration](#common-configuration), the following methods are available:

### `setShowDots(bool $showDots = true)`

Display dots on the graph lines.

### `setCurvedLines(bool $curvedLines = true)`

Display lines with curved angles. Default is `true`.

## Area
```php
$widgetsContainer->addWidget(
    SharpAreaGraphWidget::make('sales')
);
```

Along with the [common configuration](#common-configuration), the following methods are available:

### `setCurvedLines(bool $curvedLines = true)`

Display lines with curved angles. Default is `true`.

### `setOpacity(float $opacity)`

Change the opacity of the filled areas. Default is `0.4`.

### `setShowGradient(bool $showGradient = true)`

Display a gradient on top of the filled areas.

### `setStacked(bool $stacked = true)`

Stack areas on top of each other. Useful for comparing two or more series. The order of `->addGraphDataSet()` calls defines the stacking order.

### `setShowStackTotal(bool $showStackTotal = true, ?string $label = null)`

Show the total of all stacked areas in the tooltip. The label can be customized.

## Bar
```php
$widgetsContainer->addWidget(
    SharpBarGraphWidget::make('sales')
);
```

Along with the [common configuration](#common-configuration), the following methods are available:

### `setHorizontal(bool $horizontal = true)`

Display horizontal bars instead of vertical ones. Default is `false`.


## Pie

```php
$widgetsContainer->addWidget(
    SharpPieGraphWidget::make('sales')
);
```

## Common configuration

### `setRatio(string $ratio)`

This attribute is used to define the graph ratio, which will be consistent in responsive mode. The expected format is `width:height`, so for instance `16:9` (the default) or `4:3`.

### `setHeight(int $height)`

Used to set an arbitrary height, in px; if set the ratio is ignored.

### `setShowLegend(bool $showLegend = true)`

Display or not the graph legend. Default is `true`.

### `setMinimal(bool $minimal = true)`

If true, legend and axis are hidden. Default is `false`.

### `setDisplayHorizontalAxisAsTimeline(bool $displayAsTimeline = true)`

**(Line, Area, Bar)** If true, and if X axis values are valid dates, the graph will create a timeline repartition of dates, creating visual gaps between dates. Default is `false`. 

### `setEnableHorizontalAxisLabelSampling(bool $enableLabelSampling = true)`

**(Line, Area, Bar)** If true, only some labels will be displayed depending on available space. It prevents label rotation. This method has no impact when `setDisplayHorizontalAxisAsTimeline()` is called. Default is `false`.

## Data valuation

Valuation is handled by a dedicated method: `$this->addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet)`, in the Dashboard class:

```php
$this->addGraphDataSet(
    'sales',
    SharpGraphWidgetDataSet::make($values)
        ->setLabel('Sales')
        ->setColor('blue')
);
```

We use an instance of `Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet` to handle graph data. This object is built with a `$values` array which must contain numeric values, keyed by a label. Example:

```php
[
  'Pizza' => 4,
  'Hamburgers' => 12 
]
```

`SharpGraphWidgetDataSet` defines two useful setters:

- `setLabel(string $label)` to set the legend label
- `setColor(string $color)` where $color can be an HTML constant or an hexadecimal value, to set the data color.

You can chain calls to `addGraphDataSet()` to add multiple data sets, with different colors and labels.
