<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\MorphOneRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Fixtures\Picture;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class MorphOneRelationUpdaterTest extends SharpFormEloquentBaseTest
{

    /** @test */
    function we_can_create_a_morphOne_related()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $updater = new MorphOneRelationUpdater();

        $updater->update($person, "picture:file", "test.jpg");

        $this->assertDatabaseHas("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "test.jpg",
        ]);
    }

    /** @test */
    function we_can_update_a_morphOne_related()
    {
        $person = Person::create(["name" => "John Wayne"]);
        $person->picture()->create([
            "file" => "old.jpg"
        ]);

        $updater = new MorphOneRelationUpdater();

        $updater->update($person, "picture:file", "test.jpg");

        $this->assertDatabaseHas("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "test.jpg",
        ]);
    }

    /** @test */
    function we_ignore_a_morphOne_related_if_null()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $updater = new MorphOneRelationUpdater();

        $updater->update($person, "picture:file", null);

        $this->assertCount(0, Picture::all());
    }

}