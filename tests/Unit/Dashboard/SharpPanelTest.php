<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Links\LinkToEntityList;

class SharpPanelTest extends SharpTestCase
{
    /** @test */
    public function returned_array_contains_template()
    {
        $widget = SharpPanelWidget::make('name')
            ->setInlineTemplate('<b>test</b>');

        $this->assertArraySubset(
            ['template' => '<b>test</b>'],
            $widget->toArray()
        );
    }

    /** @test */
    public function returned_array_contains_the_SharpLinkTo_link()
    {
        $widget = SharpPanelWidget::make('name')
            ->setInlineTemplate('<b>test</b>')
            ->setLink(LinkToEntityList::make('entity'));

        $this->assertArraySubset(
            ['link' => url('sharp/s-list/entity')],
            $widget->toArray()
        );
    }
}
