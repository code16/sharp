<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutColumnTest extends SharpTestCase
{
    /** @test */
    public function we_can_set_a_size()
    {
        $formTab = new FormLayoutColumn(2);

        $this->assertEquals(2, $formTab->toArray()['size']);
    }

    /** @test */
    public function we_can_add_a_field()
    {
        $formTab = new FormLayoutColumn(2);
        $formTab->withSingleField('name');

        $this->assertCount(1, $formTab->toArray()['fields'][0]);
    }

    /** @test */
    public function we_can_add_multiple_fields()
    {
        $formTab = new FormLayoutColumn(2);
        $formTab->withFields('name', 'age');

        $this->assertCount(2, $formTab->toArray()['fields'][0]);
    }

    /** @test */
    public function we_can_add_a_fieldset()
    {
        $formTab = new FormLayoutColumn(2);
        $formTab->withFieldset('label', function ($layout) {
            $layout->withSingleField('name');
        });

        $this->assertCount(1, $formTab->toArray()['fields'][0]);
        $this->assertCount(1, $formTab->toArray()['fields'][0][0]['fields']);
    }
}
