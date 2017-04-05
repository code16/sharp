<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\Layout\FormLayoutField;
use Code16\Sharp\Tests\SharpTestCase;

class FormLayoutFieldTest extends SharpTestCase
{

    /** @test */
    function we_can_set_a_key()
    {
        $formTab = new FormLayoutField('name');

        $this->assertEquals('name', $formTab->toArray()["key"]);
    }

    /** @test */
    function we_can_set_a_general_size()
    {
        $formTab = new FormLayoutField('name|6');

        $this->assertArraySubset(
            ["key" => "name", "size" => 6],
            $formTab->toArray()
        );
    }

    /** @test */
    function we_can_set_a_xs_size()
    {
        $formTab = new FormLayoutField('name|6,8');

        $this->assertArraySubset(
            ["key" => "name", "size" => 6, "sizeXS" => 8],
            $formTab->toArray()
        );
    }

}