<?php

namespace Code16\Sharp\Tests\Unit\Form\Layout;

use Code16\Sharp\Form\BuildsSharpFormLayout;
use Code16\Sharp\Tests\SharpTestCase;

class BuildsSharpFormLayoutTest extends SharpTestCase
{

    /** @test */
    function we_can_add_a_tab()
    {
        $form = $this->getObjectForTrait(BuildsSharpFormLayout::class);
        $form->addTab("label");

        $this->assertCount(1, $form->buildLayout());
    }

    /** @test */
    function we_can_add_a_column()
    {
        $form = $this->getObjectForTrait(BuildsSharpFormLayout::class);
        $form->addColumn(2);

        $this->assertCount(1, $form->buildLayout());
    }

    /** @test */
    function we_can_see_layout_as_array()
    {
        $form = $this->getObjectForTrait(BuildsSharpFormLayout::class);
        $form->addTab("label");

        $this->assertArraySubset(
            ["title" => "label", "columns" => []],
            $form->buildLayout()[0]
        );

        $form2 = $this->getObjectForTrait(BuildsSharpFormLayout::class);
        $form2->addColumn(2);

        $this->assertArraySubset([
            "columns" => [
                ["size" => 2]
            ]],
            $form2->buildLayout()[0]
        );
    }
}