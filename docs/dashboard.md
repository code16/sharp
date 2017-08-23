# The Dashboard

By default, the sharp homepage is a welcome text, with the side menu to navigate. You can decide to integrate here a Dashboard, with graphs (bars and line) and stat data, or with personalized reminders for instance.

## Write the class

The Dashboard is very much like an Entity Form, except it readonly. So the first step is to create a new class extending `Code16\Sharp\Dashboard\SharpDashboard` which lead us to implement three functions:

- `buildWidgets()`, similar to Entity Form's `buildForm()`
- `buildWidgetsLayout()`, similar to `buildLayout()`
- and `buildWidgetsData()`, for the actual Dashboard data, like Entity Form's `find()` method.

### `buildWidgets()`

We're suppose to use here `$this->addWidget()` to configure all the Dashboard widgets.

    function buildWidgets()
    {
        $this->addWidget(
            SharpLineGraphWidget::make("capacities")
                ->setTitle("Spaceships by capacity")

        )->addWidget(
            SharpPanelWidget::make("activeSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
                ->setLink('spaceship')
        );
    }

As we can see in this example, we defined two widgets giving them a mandatory `key` and some optional properties depending of their type. 

Every widget has the optional following setters:

- `setTitle(string $title)` for the widget title displayed above it
- `setLink(string $entityKey, string $instanceId = null)` to make the whole widget linked to a specific entity. To link to the Entity List, pass the `$entityKey`, and add the `$instanceId` to link to the Entity Form.

And here's the full list and documentation of each widget available, for the specifics:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)


### `buildWidgetsLayout()`

The layout API is a bit different of Entity Form here, because we think in terms of rows and not columns. So for instance:

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("capacities")
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "activeSpaceships")
                    ->addWidget(6, "inactiveSpaceships");
            });
    }

We can only add rows and "full width widgets" (which are a shortcut for a single widget row). A row groups widgets in a 12-based grid.


### `buildWidgetsData()`

Widget data is set with specific methods depending of their type. The documentation is therefore split:

- [Graph](dashboard-widgets/graph.md)
- [Panel](dashboard-widgets/panel.md)

## Configure the Dashboard

Once this class written, we have to declare the form in the sharp config file:

    // config/sharp.php
    
    return [
        "entities" => [
            [...]
        ],
        "dashboard" => \App\Sharp\Dashboard::class
    ];

This will display the Dashboard on the `/sharp` home URL, and add a link in the side menu.

---
