<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormTagsFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $options = [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ];

        $formField = $this->getDefaultTags($options);

        $this->assertEquals([
                "key" => "field", "type" => "tags",
                "options" => [
                    ["id" => "1", "label" => "Elem 1"],
                    ["id" => "2", "label" => "Elem 2"],
                ], "creatable" => false,
                "createText" => "Create"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_creatable()
    {
        $formField = $this->getDefaultTags()
            ->setCreatable(true);

        $this->assertArrayContainsSubset(
            ["creatable" => true],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_createText()
    {
        $formField = $this->getDefaultTags()
            ->setCreateText("A");

        $this->assertArrayContainsSubset(
            ["createText" => "A"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxTagsCount()
    {
        $formField = $this->getDefaultTags()
            ->setMaxTagCount(2);

        $this->assertArrayContainsSubset(
            ["maxTagCount" => 2],
            $formField->toArray()
        );
    }

    /**
     * @param array|null $options
     * @return SharpFormTagsField
     */
    private function getDefaultTags($options = null)
    {
        return SharpFormTagsField::make("field", $options ?: [
            "1" => "Elem 1",
            "2" => "Elem 2"
        ]);
    }
}