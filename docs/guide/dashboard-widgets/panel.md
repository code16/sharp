# Panel widget

This widget is intended to display any useful information to the user, based on a custom blade template.

## Attributes (setters)

```php
$widgetsContainer->addWidget(
    SharpPanelWidget::make('messages')
        ->setTemplate('my/blade/template')
```

The Panel needs a blade template to be rendered:

### `setTemplate(View|string $template)`
Pass a blade view path, or a blade content.

## Data valuation

Valuation is handled by a dedicated `$this->setPanelData(string $panelWidgetKey, array $data)` in the Dashboard class.

Example:

```php
class MyDashboard extends SharpDashboard
{
    // ...
    
    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer
            ->addWidget(
                SharpPanelWidget::make('my_panel')
                    ->setTitle('My custom panel')
                    ->setTemplate(view('sharp.templates.dashboard_panel')) // Must be an existing blade view
            );
    }
    
    public function buildWidgetsData(): void
    {
        // ...
        
        $this->setPanelData('my_panel', [
            // Add here every data required by the blade template
            'author' => $author,
            'post_count' => $count,
        ]);
    }
}
```

