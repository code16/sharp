<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

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
//
//    /** @test */
//    function we_can_get_layout()
//    {
//        $form = new class extends SharpFormTestForm {
//            function buildFormFields()
//            {
//                $this->addField(SharpFormTextField::make("name"))
//                    ->addField(SharpFormTextField::make("age"));
//            }
//            function buildFormLayout()
//            {
//                $this->addColumn(6, function($column) {
//                    $column->withSingleField("name");
//                })->addColumn(6, function($column) {
//                    $column->withSingleField("age");
//                });
//            }
//        };
//
//        $this->assertEquals([
//            "tabbed" => true,
//            "tabs" => [[
//                "title" => "one",
//                "columns" => [[
//                    "size" => 6,
//                    "fields" => [[
//                        [
//                            "key" => "name",
//                            "size" => 12,
//                            "sizeXS" => 12
//                        ]
//                    ]]
//                ], [
//                    "size" => 6,
//                    "fields" => [[
//                        [
//                            "key" => "age",
//                            "size" => 12,
//                            "sizeXS" => 12
//                        ]
//                    ]]
//                ]]
//            ]]
//        ], $form->formLayout());
//    }
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

abstract class SharpDashboardTestDashboard extends SharpDashboard
{
}