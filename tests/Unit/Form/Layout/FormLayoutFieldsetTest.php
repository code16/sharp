<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutFieldsetTest extends SharpTestCase
{

    /** @test */
    function we_can_set_a_legend()
    {
        $formTab = new FormLayoutFieldset('legend');

        $this->assertEquals('legend', $formTab->toArray()["legend"]);
    }

    /** @test */
    function we_can_add_a_field()
    {
        $formTab = new FormLayoutFieldset('legend');
        $formTab->withSingleField("name");

        $this->assertCount(1, $formTab->toArray()["fields"][0]);
    }

    /** @test */
    function we_can_add_multiple_fields()
    {
        $formTab = new FormLayoutFieldset('legend');
        $formTab->withFields("name", "age");

        $this->assertCount(2, $formTab->toArray()["fields"][0]);
    }

}