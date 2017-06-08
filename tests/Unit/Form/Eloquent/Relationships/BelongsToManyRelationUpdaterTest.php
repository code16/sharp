<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\BelongsToManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class BelongsToManyRelationUpdaterTest extends SharpFormEloquentBaseTest
{
    
    /** @test */
    function we_can_update_a_belongsToMany_relation()
    {
        $person1 = Person::create(["name" => "A"]);
        $person2 = Person::create(["name" => "B"]);

        $updater = new BelongsToManyRelationUpdater();

        $updater->update($person1, "friends", [$person2->id]);

        $this->assertDatabaseHas("friends", [
            "person1_id" => $person1->id,
            "person2_id" => $person2->id,
        ]);
    }

    /** @test */
    function we_can_update_an_existing_belongsToMany_relation()
    {
        $person1 = Person::create(["name" => "A"]);
        $person2 = Person::create(["name" => "B"]);
        $person3 = Person::create(["name" => "C"]);

        $person1->friends()->sync([$person2->id]);

        $updater = new BelongsToManyRelationUpdater();

        $updater->update($person1, "friends", [$person3->id]);

        $this->assertDatabaseHas("friends", [
            "person1_id" => $person1->id,
            "person2_id" => $person3->id,
        ]);

        $this->assertDatabaseMissing("friends", [
            "person1_id" => $person1->id,
            "person2_id" => $person2->id,
        ]);
    }
}