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
    public function we_can_insert_a_field()
    {
        $formTab = new FormLayoutColumn(2);
        $formTab->withSingleField('name');
        $formTab->insertSingleFieldAt(0, 'age');

        $this->assertEquals('age', $formTab->toArray()['fields'][0][0]['key']);
        $this->assertEquals('name', $formTab->toArray()['fields'][1][0]['key']);
    }

    /** @test */
    public function we_can_insert_multiple_fields()
    {
        $formTab = new FormLayoutColumn(2);
        $formTab->withSingleField('name');
        $formTab->insertFieldsAt(0, 'age', 'size');

        $this->assertEquals('age', $formTab->toArray()['fields'][0][0]['key']);
        $this->assertEquals('size', $formTab->toArray()['fields'][0][1]['key']);
        $this->assertEquals('name', $formTab->toArray()['fields'][1][0]['key']);
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
