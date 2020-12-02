<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpBarGraphWidgetTest extends SharpTestCase
{

    /** @test */
    function returned_array_contains_bar_display()
    {
        $widget = SharpBarGraphWidget::make("name");

        $this->assertArraySubset(
            ["display" => "bar"],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_contains_default_ratio()
    {
        $widget = SharpBarGraphWidget::make("name");

        $this->assertArraySubset(
            ["ratioX" => 16, "ratioY" => 9],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_a_specific_ratio()
    {
        $widget = SharpBarGraphWidget::make("name")
            ->setRatio("2:3");

        $this->assertArraySubset(
            ["ratioX" => 2, "ratioY" => 3],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_minimal_attribute()
    {
        $widget = SharpBarGraphWidget::make("name")
            ->setMinimal();

        $this->assertArraySubset(
            ["minimal" => true],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_showLegend_attribute()
    {
        $widget = SharpBarGraphWidget::make("name")
            ->setShowLegend(false);

        $this->assertArraySubset(
            ["showLegend" => false],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_height_attribute()
    {
        $widget = SharpBarGraphWidget::make("name")
            ->setHeight(150);

        $this->assertArraySubset(
            ["height" => 150],
            $widget->toArray()
        );
    }
}