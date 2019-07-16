<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpListGroupWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpListGroupTest extends SharpTestCase
{

    /** @test */
    function returned_array_contains_with_counts()
    {
        $widget = SharpListGroupWidget::make("name")
            ->setWithCounts();

        $this->assertArrayContainsSubset(
            ["withCounts" => true],
            $widget->toArray()
        );
    }
}