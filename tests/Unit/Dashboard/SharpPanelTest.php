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

        $this->assertArrayContainsSubset(
            ["template" => "<b>test</b>"],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_contains_entity_link()
    {
        $widget = SharpPanelWidget::make("name")
            ->setInlineTemplate('<b>test</b>')
            ->setLink('entity');

        $this->assertArrayContainsSubset(
            ["link" => url("sharp/list/entity")],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_contains_instance_link()
    {
        $widget = SharpPanelWidget::make("name")
            ->setInlineTemplate('<b>test</b>')
            ->setLink('entity', 1);

        $this->assertArrayContainsSubset(
            ["link" => url("sharp/form/entity/1")],
            $widget->toArray()
        );
    }

    /** @test */
    function returned_array_contains_querystring_params()
    {
        $widget = SharpPanelWidget::make("name")
            ->setInlineTemplate('<b>test</b>')
            ->setLink('entity', null, ["page" => 1, "filter_one" => "something"]);

        $this->assertArrayContainsSubset(
            ["link" => url("sharp/list/entity") . "?page=1&filter_one=something"],
            $widget->toArray()
        );
    }
}