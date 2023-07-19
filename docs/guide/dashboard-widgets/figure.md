# Figure widget

This widget is intended to display a single figure, with an optional evolution indicator.

## Attributes (setters)

```php
$widgetsContainer->addWidget(
    SharpFigureWidget::make('sales')
        ->setTitle('Total sales in €')
        ->setLink(LinkToEntityList::make('orders')->addFilter(StateFilter::class, 'confirmed'));
```

Note that the `setLink()` method is expecting a [LinkTo... instance](../link-to.md).

## Data valuation

Valuation is handled by a dedicated `$this->setFigureData(string $figureWidgetKey, string $figure, ?string $unit = null, ?string $evolution = null)` in the Dashboard class:

```php
class MyDashboard extends \Code16\Sharp\Dashboard\SharpDashboard
{
    // ...
    
    public function buildWidgetsData(): void
    {
        $this->setFigureData('sales', 135, 'k€', '+3%');
    }
}
```

Of course in a real word project you would probably fetch the data from your database, and compute the evolution from a comparison period. The fourth parameter, `$evolution`, is optional and will display a green figure with a ↑ when starting with a `+`, and a red figure with a ↓ when starting with a `-` sign.
