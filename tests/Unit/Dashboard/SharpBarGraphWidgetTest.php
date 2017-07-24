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
}