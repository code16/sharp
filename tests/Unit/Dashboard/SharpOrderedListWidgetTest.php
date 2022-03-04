<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Tests\SharpTestCase;

class SharpOrderedListWidgetTest extends SharpTestCase
{
    /** @test */
    public function we_can_define_html_attribute()
    {
        $widget = SharpOrderedListWidget::make('name')
            ->setHtml();

        $this->assertArraySubset(
            ['html' => true],
            $widget->toArray()
        );
    }
}
