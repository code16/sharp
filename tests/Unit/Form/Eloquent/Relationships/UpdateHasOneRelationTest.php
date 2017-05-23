<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\UpdateHasOneRelation;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class UpdateHasOneRelationTest extends SharpFormEloquentBaseTest
{
    
    /** @test */
    function we_can_update_a_hasOne_relation()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        $son = Person::create(["name" => "John Wayne"]);

        $updater = new UpdateHasOneRelation();

        $updater->update($mother, "elderSon", $son->id);

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "mother_id" => $mother->id
        ]);
    }
}