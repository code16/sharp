<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
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
            "display" => "bar"
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
//
//    /** @test */
//    function we_can_get_instance()
//    {
//        $form = new class extends SharpFormTestForm {
//            function find($id): array
//            {
//                return [
//                    "name" => "John Wayne",
//                    "age" => 22,
//                    "job" => "actor"
//                ];
//            }
//            function buildFormFields()
//            {
//                $this->addField(SharpFormTextField::make("name"))
//                    ->addField(SharpFormTextField::make("age"));
//            }
//        };
//
//        $this->assertEquals([
//            "name" => "John Wayne",
//            "age" => 22
//        ], $form->instance(1));
//    }
}

class SharpDashboardTestDashboard extends SharpDashboard
{
    protected function buildWidgets() {}
    protected function buildWidgetsLayout() {}
    protected function getWidgetsData() { return []; }
}