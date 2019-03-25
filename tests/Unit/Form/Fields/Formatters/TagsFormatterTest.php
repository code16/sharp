<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\TagsFormatter;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Tests\SharpTestCase;

class TagsFormatterTest extends SharpTestCase
{

    /** @test */
    function we_can_format_ids_to_front()
    {
        $formatter = new TagsFormatter;
        $field = SharpFormTagsField::make("tags", $this->getTagsData());

        $this->assertEquals([["id"=>1]], $formatter->toFront($field, 1));
        $this->assertEquals([["id"=>1],["id"=>2]], $formatter->toFront($field, [1,2]));
    }

    /** @test */
    function we_can_format_objects_to_front()
    {
        $formatter = new TagsFormatter;
        $field = SharpFormTagsField::make("tags", $this->getTagsData());

        $this->assertEquals(
            [["id"=>1]],
            $formatter->toFront($field, [(object)["id"=>1,"name"=>"A"]])
        );
        $this->assertEquals(
            [["id"=>1],["id"=>2]],
            $formatter->toFront($field, [(object)["id"=>1,"name"=>"A"], (object)["id"=>2,"name"=>"B"]])
        );
    }

    /** @test */
    function we_can_format_arrays_to_front()
    {
        $formatter = new TagsFormatter;
        $field = SharpFormTagsField::make("tags", $this->getTagsData());

        $this->assertEquals(
            [["id"=>1]],
            $formatter->toFront($field, [["id"=>1,"name"=>"A"]])
        );
        $this->assertEquals(
            [["id"=>1],["id"=>2]],
            $formatter->toFront($field, [["id"=>1,"name"=>"A"], ["id"=>2,"name"=>"B"]])
        );
    }

    /** @test */
    function we_can_format_objects_to_front_with_a_defined_id_attribute()
    {
        $formatter = new TagsFormatter;
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setIdAttribute("number");

        $this->assertEquals(
            [["id"=>1]],
            $formatter->toFront($field, [(object)["number"=>1,"name"=>"A"]])
        );
        $this->assertEquals(
            [["id"=>1],["id"=>2]],
            $formatter->toFront($field, [(object)["number"=>1,"name"=>"A"], (object)["number"=>2,"name"=>"B"]])
        );
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData());

        $this->assertEquals([["id"=>1]], $formatter->fromFront($field, $attribute, [["id"=>1]]));
        $this->assertEquals([["id"=>1],["id"=>2]], $formatter->fromFront($field, $attribute, [["id"=>1],["id"=>2]]));
    }

    /** @test */
    function we_strip_non_configured_values_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData());

        $this->assertEquals([["id"=>1],["id"=>2]], $formatter->fromFront(
            $field, $attribute, [["id"=>1],["id"=>2],["id"=>3]])
        );
    }

    /** @test */
    function we_handle_creatable_attribute_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setCreatable()
            ->setCreateAttribute("name");

        $this->assertEquals([["id"=>1],["id"=>2],["id"=>null,"name"=>"green"]], $formatter->fromFront(
            $field, $attribute, [["id"=>1],["id"=>2],["id"=>null,"label"=>"green"]])
        );
    }

    /** @test */
    function we_strip_null_ids_if_creatable_attribute_is_false_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setCreatable(false)
            ->setCreateAttribute("name");

        $this->assertEquals([["id"=>1],["id"=>2]], $formatter->fromFront(
            $field, $attribute, [["id"=>1],["id"=>2],["id"=>null,"label"=>"green"]])
        );
    }

    /** @test */
    function we_handle_id_attribute_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setIdAttribute("number");

        $this->assertEquals([["number"=>1]], $formatter->fromFront($field, $attribute, [["id"=>1]]));
        $this->assertEquals([["number"=>1],["number"=>2]], $formatter->fromFront($field, $attribute, [["id"=>1],["id"=>2]]));
    }

    /** @test */
    function we_handle_id_and_creatable_attribute_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setIdAttribute("number")
            ->setCreatable()
            ->setCreateAttribute("name");

        $this->assertEquals(
            [["number"=>null,"name"=>"green"]],
            $formatter->fromFront($field, $attribute, [["id"=>null,"label"=>"green"]])
        );
    }

    /** @test */
    function we_handle_additional_create_attributes_from_front()
    {
        $formatter = new TagsFormatter;
        $attribute = "attribute";
        $field = SharpFormTagsField::make("tags", $this->getTagsData())
            ->setCreatable()
            ->setCreateAdditionalAttributes([
                "group" => "test"
            ])
            ->setCreateAttribute("name");

        $this->assertEquals(
            [["id"=>1],["id"=>2],["id"=>null,"name"=>"green","group"=>"test"]],
            $formatter->fromFront(
                $field, $attribute, [["id"=>1],["id"=>2],["id"=>null,"label"=>"green"]]
            )
        );
    }

    /**
     * @return array
     */
    protected function getTagsData()
    {
        return [
            1 => "red",
            2 => "blue",
        ];
    }

}