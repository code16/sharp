<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Transformers;

use Code16\Sharp\Form\Eloquent\Transformers\EloquentFormTagsTransformer;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class EloquentTagsTransformerTest extends SharpFormEloquentBaseTest
{

    /** @test */
    function we_can_transform_a_regular_attribute()
    {
        $person = Person::create(["name" => "A"]);
        $friend1 = Person::create(["name" => "John Wayne"]);
        $friend2 = Person::create(["name" => "Jane Wayne"]);
        $person->friends()->sync([$friend1->id, $friend2->id]);

        $transformer = new EloquentFormTagsTransformer("name", "id");

        $this->assertEquals([
            ["id" => $friend1->id, "label" => "John Wayne"],
            ["id" => $friend2->id, "label" => "Jane Wayne"],
        ], $transformer->apply($person, "friends"));
    }

    /** @test */
    function we_can_transform_an_attribute_with_a_closure()
    {
        $person = Person::create(["name" => "A"]);
        $friend1 = Person::create(["name" => "John Wayne"]);
        $friend2 = Person::create(["name" => "Jane Wayne"]);
        $person->friends()->sync([$friend1->id, $friend2->id]);

        $transformer = new EloquentFormTagsTransformer(function($person) {
            return $person->id . " - " . $person->name;
        }, "id");

        $this->assertEquals([
            ["id" => $friend1->id, "label" => $friend1->id . " - John Wayne"],
            ["id" => $friend2->id, "label" => $friend2->id . " - Jane Wayne"],
        ], $transformer->apply($person, "friends"));
    }
}