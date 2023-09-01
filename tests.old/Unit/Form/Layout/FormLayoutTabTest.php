<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutTabTest extends SharpTestCase
{
    /** @test */
    public function we_can_set_a_title()
    {
        $formTab = new FormLayoutTab('test');

        $this->assertEquals('test', $formTab->toArray()['title']);
    }

    /** @test */
    public function we_can_add_a_column()
    {
        $formTab = new FormLayoutTab('test');
        $formTab->addColumn(2);

        $this->assertCount(1, $formTab->toArray()['columns']);
    }

    /** @test */
    public function we_can_add_several_columns()
    {
        $formTab = new FormLayoutTab('test');
        $formTab->addColumn(2);
        $formTab->addColumn(2);
        $formTab->addColumn(2);

        $this->assertCount(3, $formTab->toArray()['columns']);
    }
}
