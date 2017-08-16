<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpDashboardTest extends SharpTestCase
{
    /** @test */
    function we_can_get_widgets()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            protected function buildWidgets()
            {
                $this->addWidget(
                    SharpBarGraphWidget::make("widget")
                );
            }
        };

        $this->assertEquals(["widget" => [
            "key" => "widget",
            "type" => "graph",
            "display" => "bar",
            "ratioX" => 16,
            "ratioY" => 9,
        ]], $dashboard->widgets());
    }

    /** @test */
    function we_can_get_widgets_layout()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            protected function buildWidgets()
            {
                $this->addWidget(SharpBarGraphWidget::make("widget"))
                    ->addWidget(SharpBarGraphWidget::make("widget2"))
                    ->addWidget(SharpBarGraphWidget::make("widget3"));
            }
            protected function buildWidgetsLayout()
            {
                $this->addFullWidthWidget("widget")
                    ->addRow(function(DashboardLayoutRow $row) {
                        $row->addWidget(4, "widget2")
                            ->addWidget(8, "widget3");
                    });
            }
        };

        $this->assertEquals([
            "rows" => [
                [
                    ["key" => "widget", "size" => 12]
                ], [
                    ["key" => "widget2", "size" => 4],
                    ["key" => "widget3", "size" => 8],
                ]
            ]
        ], $dashboard->widgetsLayout());
    }

    /** @test */
    function we_can_get_graph_widget_data()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            protected function buildWidgets()
            {
                $this->addWidget(SharpBarGraphWidget::make("widget"));
            }
            protected function buildWidgetsData()
            {
                $this->addGraphDataSet("widget", SharpGraphWidgetDataSet::make([
                    "a" => 10, "b" => 20, "c" => 30,
                ])->setLabel("test")->setColor("blue"));
            }
        };

        $this->assertEquals([
            "widget" => [
                "key" => "widget",
                "datasets" => [
                    [
                        "data" => [10,20,30],
                        "label" => "test",
                        "color" => "blue"
                    ]
                ], "labels" => [
                    "a", "b", "c"
                ]
            ]
        ], $dashboard->data());
    }

    /** @test */
    function we_can_get_graph_widget_data_with_multiple_datasets()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            protected function buildWidgets()
            {
                $this->addWidget(SharpBarGraphWidget::make("widget"));
            }
            protected function buildWidgetsData()
            {
                $this->addGraphDataSet("widget", SharpGraphWidgetDataSet::make([
                    "a" => 10, "b" => 20, "c" => 30,
                ])->setLabel("test")->setColor("blue"));
                $this->addGraphDataSet("widget", SharpGraphWidgetDataSet::make([
                    "a" => 40, "b" => 50, "c" => 60,
                ])->setLabel("test2")->setColor("red"));
            }
        };

        $this->assertEquals([
            "widget" => [
                "key" => "widget",
                "datasets" => [
                    [
                        "data" => [10,20,30],
                        "label" => "test",
                        "color" => "blue"
                    ], [
                        "data" => [40,50,60],
                        "label" => "test2",
                        "color" => "red"
                    ]
                ], "labels" => [
                    "a", "b", "c"
                ]
            ]
        ], $dashboard->data());
    }

    /** @test */
    function we_can_get_panel_widget_data()
    {
        $dashboard = new class extends SharpDashboardTestDashboard {
            protected function buildWidgets()
            {
                $this->addWidget(
                    SharpPanelWidget::make("widget")->setInlineTemplate('<b>Hello {{user}}</b>')
                );
            }
            protected function buildWidgetsData()
            {
                $this->setPanelData("widget", ["user" => "John Wayne"]);
            }
        };

        $this->assertEquals([
            "widget" => [
                "key" => "widget",
                "data" => [
                    "user" => "John Wayne"
                ]
            ]
        ], $dashboard->data());
    }
}

class SharpDashboardTestDashboard extends SharpDashboard
{
    protected function buildWidgets() {}
    protected function buildWidgetsLayout() {}
    protected function buildWidgetsData() { }
}