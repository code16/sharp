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

        $this->assertArrayContainsSubset(
            ["display" => "bar"],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_contains_default_ratio()
    {
        $widget = SharpBarGraphWidget::make("name");

        $this->assertArrayContainsSubset(
            ["ratioX" => 16, "ratioY" => 9],
            $widget->toArray()
        );
    }

    /** @test */
    function we_can_define_a_specific_ratio()
    {
        $widget = SharpBarGraphWidget::make("name")
            ->setRatio("2:3");

        $this->assertArrayContainsSubset(
            ["ratioX" => 2, "ratioY" => 3],
            $widget->toArray()
        );
    }
}