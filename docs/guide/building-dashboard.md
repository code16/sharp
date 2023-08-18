# Create a Dashboard

A Dashboard is a good way to present synthetic data to the user, with graphs, stats, or personalized reminders for instance.

## Generator

```bash
php artisan sharp:make:dashboard <class_name>
```

## Write the class

The first step is to create a new class extending `Code16\Sharp\Dashboard\SharpDashboard` which leads us to implement three functions:

- `buildWidgets(WidgetsContainer $widgetsContainer)`,
- `buildDashboardLayout(DashboardLayout $dashboardLayout)`,
- and `buildWidgetsData(DashboardQueryParams $params)`, for the actual Dashboard data

### `buildWidgets(WidgetsContainer $widgetsContainer): void`

This method is meant to host the code responsible for the declaration and configuration of each widget. This must be done by calling `$widgetsContainer->addWidget()`:

```php
class SalesDashboard extends SharpDashboard
{
    // [...]
    function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer
            ->addWidget(
                SharpLineGraphWidget::make('sales')
                    ->setTitle('Sales evolution')
            )
            ->addWidget(
                SharpFigureWidget::make('pendingOrders')
                    ->setTitle('Pending orders')
                    ->setLink(LinkToEntityList::make('orders')->addFilter(StateFilter::class, 'pending'))
            );
    }
}
```

As we can see in this example, we defined two widgets giving them a mandatory `key` and some optional properties.

Every widget has the optional following setters:

- `setTitle(string $title)` for the widget title displayed above it
- `setLink(SharpLinkTo $sharpLinkTo)` to make the whole widget linked to a specific page (see [dedicated SharpLinkTo documentation](link-to.md))

And here's the full list and documentation of each widget available, for the specifics:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)
- [Figure](dashboard-widgets/figure.md)
- [OrderedList](dashboard-widgets/ordered-list.md)

### `buildDashboardLayout(DashboardLayout $dashboardLayout): void`

The layout API is a bit different from Forms or Show Pages here, because we think in terms of rows and not columns.

```php
function buildDashboardLayout(DashboardLayout $dashboardLayout): void
{
    $dashboardLayout
        ->addSection('Posts', function (DashboardLayoutSection $section) {
            $section->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'draft_panel')
                    ->addWidget(6, 'online_panel');
            });
        })
        ->addSection('Stats', function (DashboardLayoutSection $section) {
            $section->addFullWidthWidget('visits_line');
        });
}
```

Note that:
- Sections are optional but useful to group related widgets; you can add rows directly to the layout if you don’t need them.
- Rows group widgets in a 12-based grid.

### `buildWidgetsData(): void`

Widget data is set with specific methods depending on their type. The documentation is therefore split:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)
- [Figure](dashboard-widgets/figure.md)
- [OrderedList](dashboard-widgets/ordered-list.md)

## Configure the Dashboard

A Dashboard must have his own [Entity class, as documented here](entity-class.md). 

Once this class (`CompanyDashboardEntity` for instance) written, we have to declare it the sharp config file:

```php
// config/sharp.php

return [
    "entities" => [
        // ...
    ],
    "dashboards" => [
        "company_dashboard" => \App\Sharp\CompanyDashboardEntity::class
    ],
    // ...
];
```

In the menu, like an Entity, a Dashboard can be displayed anywhere.

```php
class AppSharpMenu extends SharpMenu
{
    public function build(): self
    {
        return $this
            ->addEntityLink('company_dashboard', 'Dashboard', 'fas fa-chart-line')
            // ...
    }
}
```

## Dashboard commands

Like Entity Lists, Commands can be declared in a Dashboard with `getDashboardCommands()` : [see the Command documentation](commands.md).

And like Show Pages, Commands can be visually attached to a specific section:

```php
protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
{
    $dashboardLayout
        ->addSection('Posts', function (DashboardLayoutSection $section) {
            // ...
        })
        ->addSection('Stats', function (DashboardLayoutSection $section) {
            $section
                ->setKey('stats-section') // <- define a key here...
                ->addRow(function (DashboardLayoutRow $row) {
                    // ...
                });
        });
}

public function getFilters(): ?array
{
    return [
        'stats-section' => [
            PeriodRequiredFilter::class,
        ],
    ];
}

public function getDashboardCommands(): ?array
{
    return [
        'stats-section' => [ // <- use the section key here...
            ExportStatsAsCsvCommand::class,
        ],
    ];
}
```

## Dashboard filters

Just like Entity Lists, Dashboard can display filters, as [documented on the Filter page](filters.md).

And very much like Commands, Filters can be visually attached to a specific section of the dashboard:

```php
public function getFilters(): ?array
{
    return [
        'stats-section' => [ // <- must be a section key
            PeriodRequiredFilter::class,
        ],
    ];
}
```

## Dashboard policy

You can define a Policy for a Dashboard; [see the authorization documentation](entity-authorizations.md).
