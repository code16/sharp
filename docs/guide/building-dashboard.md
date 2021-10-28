# Create a Dashboard

A Dashboard is a good way to present synthetic data to the user, with graphs, stats, or personalized reminders for instance.

## Generator

```bash
php artisan sharp:make:dashboard <class_name>
```

## Write the class

The first step is to create a new class extending `Code16\Sharp\Dashboard\SharpDashboard` which leads us to implement
three functions:

- `buildWidgets(WidgetsContainer $widgetsContainer)`,
- `buildDashboardLayout(DashboardLayout $dashboardLayout)`,
- and `buildWidgetsData(DashboardQueryParams $params)`, for the actual Dashboard data

### `buildWidgets(WidgetsContainer $widgetsContainer): void`

This method is meant to host the code responsible for the declaration and configuration of each widget. This must be
done by calling `$widgetsContainer->addWidget()`:

```php
function buildWidgets(WidgetsContainer $widgetsContainer): void
{
    $widgetsContainer
        ->addWidget(
            SharpLineGraphWidget::make("capacities")
                ->setTitle("Spaceships by capacity")
        )
        ->addWidget(
            SharpPanelWidget::make("activeSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
                ->setLink('spaceship')
        );
}
```

As we can see in this example, we defined two widgets giving them a mandatory `key` and some optional properties.

Every widget has the optional following setters:

- `setTitle(string $title)` for the widget title displayed above it
- `setLink(SharpLinkTo $sharpLinkTo)` to make the whole widget linked to a specific page (
  see [dedicated SharpLinkTo documentation](link-to.md))

And here's the full list and documentation of each widget available, for the specifics:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)
- [OrderedList](dashboard-widgets/ordered-list.md)

### `buildDashboardLayout(DashboardLayout $dashboardLayout): void`

The layout API is a bit different from Forms or Show Pages here, because we think in terms of rows and not columns. So
for instance:

```php
function buildDashboardLayout(DashboardLayout $dashboardLayout): void
{
    $dashboardLayout
        ->addFullWidthWidget("capacities")
        ->addRow(function(DashboardLayoutRow $row) {
            $row->addWidget(6, "activeSpaceships")
                ->addWidget(6, "inactiveSpaceships");
        });
}
```

We can only add rows and "full width widgets" (which are a shortcut for a single widget row). A row groups widgets in a 12-based grid.

### `buildWidgetsData(): void`

Widget data is set with specific methods depending on their type. The documentation is therefore split:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)
- [OrderedList](dashboard-widgets/ordered-list.md)

## Configure the Dashboard

Once this class written, we have to declare the form in the sharp config file:

```php
// config/sharp.php

return [
    "entities" => [
        [...]
    ],
    "dashboards" => [
        "company_dashboard" => [
            "view" => \App\Sharp\CompanyDashboard::class
        ]
    ],
    [...]
    "menu" => [
        [
            "label" => "Company",
            "entities" => [
                [
                    "label" => "Dashboard",
                    "icon" => "fa-dashboard",
                    "dashboard" => "company_dashboard"
                ],
                [...]
            ]
        ]
    ]
];
```

In the menu, like an Entity, a Dashboard can be displayed anywhere.

## Dashboard filters

Just like EntityLists, Dashboard can display filters, as [documented on the Filter page](filters.md).

## Dashboard commands

Like again EntityLists, Commands can be attached to a Dashboard: [see the Command documentation](commands.md).

## Dashboard policies

You can define a Policy for a Dashboard:

```php
// config/sharp.php

return [
    "entities" => [
        [...]
    ],
    "dashboards" => [
        "company_dashboard" => [
            "view" => \App\Sharp\CompanyDashboard::class,
            "policy" => \App\Sharp\Policies\CompanyDashboardPolicy::class,
        ]
    ],
    [...]
];
```

And the policy class can be pretty straightforward, since the only available action is `view`:

```php
class CompanyDashboardPolicy
{
    public function view(User $user)
    {
        return $user->hasGroup("boss");
    }
}
```

