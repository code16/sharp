<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Tests\SharpTestCase;

class AutocompleteFormatterTest extends SharpTestCase
{
    use WithSimpleFormatterTest;

    /** @test */
    function we_can_format_value_to_front()
    {
        $this->checkSimpleFormatterToFront(
            SharpFormAutocompleteField::make("text", "local"),
            new AutocompleteFormatter
        );
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $this->checkSimpleFormatterFromFront(
            SharpFormAutocompleteField::make("text", "local"),
            new AutocompleteFormatter,
            "attribute");
    }
//
//    /** @test */
//    function we_can_format_value_for_in_local_mode_to_front()
//    {
//        $formatter = new AutocompleteFormatter;
//        $field = SharpFormAutocompleteField::make("field", "local")
//            ->setLocalValues([1,2,3]);
//
//        $this->assertEquals(1, $formatter->toFront($field, 1));
//        $this->assertEquals(1, $formatter->toFront($field, ["id"=>1, "something"=>"else"]));
//    }
//
//    /** @test */
//    function we_can_format_value_for_in_remote_mode_to_front()
//    {
//        $formatter = new AutocompleteFormatter;
//        $field = SharpFormAutocompleteField::make("field", "remote");
//
//        $this->assertEquals(
//            ["id"=>1, "label"=>"text"],
//            $formatter->toFront($field, ["id"=>1, "label"=>"text", "something"=>"else"])
//        );
//    }
//
//    /** @test */
//    function we_can_define_id_and_label_attributes_in_remote_mode_to_front()
//    {
//        $formatter = new AutocompleteFormatter;
//        $field = SharpFormAutocompleteField::make("field", "remote")
//            ->setItemIdAttribute("number")
//            ->setItemLabelAttribute("name");
//
//        $this->assertEquals(
//            ["id"=>1, "label"=>"text"],
//            $formatter->toFront($field, ["number"=>1, "name"=>"text", "something"=>"else"])
//        );
//    }
//
//    /** @test */
//    function we_can_format_value_from_front()
//    {
//        $formatter = new AutocompleteFormatter;
//        $field = SharpFormAutocompleteField::make("field", "local");
//
//        $this->assertEquals(1, $formatter->fromFront($field, "attribute", 1));
//    }
}