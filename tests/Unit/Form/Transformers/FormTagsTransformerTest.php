<?php

namespace Code16\Sharp\Tests\Unit\Form\Transformers;

use Code16\Sharp\Tests\SharpTestCase;

class FormTagsTransformerTest extends SharpTestCase
{

//    /** @test */
//    function we_can_transform_a_regular_attribute()
//    {
//        $boat = new BoatTest();
//        $boat->passengers[] = new PassengerTest(1, "Bob");
//        $boat->passengers[] = new PassengerTest(2, "Mary");
//
//        $transformer = new FormTagsTransformer("name", "id");
//
//        $this->assertEquals([
//            ["id" => 1, "label" => "Bob"],
//            ["id" => 2, "label" => "Mary"],
//        ], $transformer->apply($boat, "passengers"));
//    }
//
//    /** @test */
//    function we_can_transform_an_attribute_with_a_closure()
//    {
//        $boat = new BoatTest();
//        $boat->passengers[] = new PassengerTest(1, "Bob");
//        $boat->passengers[] = new PassengerTest(2, "Mary");
//
//        $transformer = new FormTagsTransformer(function ($passenger) {
//            return $passenger->id . " - " . $passenger->name;
//        }, "id");
//
//        $this->assertEquals([
//            ["id" => 1, "label" => "1 - Bob"],
//            ["id" => 2, "label" => "2 - Mary"],
//        ], $transformer->apply($boat, "passengers"));
//    }
}

class BoatTest {
    public $passengers = [];
}

class PassengerTest {
    public $id;
    public $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}