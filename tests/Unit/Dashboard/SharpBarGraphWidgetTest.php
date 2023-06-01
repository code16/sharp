<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpBarGraphWidgetTest extends SharpTestCase
{
    /** @test */
    public function returned_array_contains_bar_display()
    {
        $widget = SharpBarGraphWidget::make('name');
        
        $this->assertEquals('bar', $widget->toArray()['display']);
    }

    /** @test */
    public function returned_array_contains_default_ratio()
    {
        $widget = SharpBarGraphWidget::make('name');

        $this->assertEquals(16, $widget->toArray()['ratioX']);
        $this->assertEquals(9, $widget->toArray()['ratioY']);
    }

    /** @test */
    public function we_can_define_a_specific_ratio()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setRatio('2:3');

        $this->assertEquals(2, $widget->toArray()['ratioX']);
        $this->assertEquals(3, $widget->toArray()['ratioY']);
    }

    /** @test */
    public function we_can_define_minimal_attribute()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setMinimal();

        $this->assertTrue($widget->toArray()['minimal']);
    }

    /** @test */
    public function we_can_define_showLegend_attribute()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setShowLegend(false);

        $this->assertFalse($widget->toArray()['showLegend']);
    }

    /** @test */
    public function we_can_define_height_attribute()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setHeight(150);
        
        $this->assertEquals(150, $widget->toArray()['height']);
    }

    /** @test */
    public function we_can_define_displayHorizontalAxisAsTimeline_attribute()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setDisplayHorizontalAxisAsTimeline();
        
        $this->assertTrue($widget->toArray()['dateLabels']);
    }

    /** @test */
    public function we_can_define_horizontal_option_attribute()
    {
        $widget = SharpBarGraphWidget::make('name')
            ->setHorizontal();
        
        $this->assertTrue($widget->toArray()['options']['horizontal']);
    }
}
