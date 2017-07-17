<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpPanelTest extends SharpTestCase
{

    /** @test */
    function returned_array_contains_template()
    {
        $widget = SharpPanelWidget::make("name")
            ->setInlineTemplate('<b>test</b>');

        $this->assertArraySubset(
            ["template" => "<b>test</b>"],
            $widget->toArray()
        );
    }
}