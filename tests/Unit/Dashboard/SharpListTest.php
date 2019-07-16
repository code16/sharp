<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpListWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpListTest extends SharpTestCase
{

    /** @test */
    function returned_array_contains_with_counts()
    {
        $widget = SharpListWidget::make("name")
            ->setWithCounts();

        $this->assertArrayContainsSubset(
            ["withCounts" => true],
            $widget->toArray()
        );
    }
}