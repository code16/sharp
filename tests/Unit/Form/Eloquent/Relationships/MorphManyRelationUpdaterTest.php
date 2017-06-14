<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\MorphManyRelationUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class MorphManyRelationUpdaterTest extends SharpFormEloquentBaseTest
{
    use TestWithSharpList;

    /** @test */
    function we_can_create_a_morphMany_related()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $updater = new MorphManyRelationUpdater();

        $updater->update($person, "pictures", $this->formatData([[
            "id" => [
                "value" => null, "valuator" => null, "field" => null
            ], "file" => [
                "value" => "test.jpg", "valuator" => null, "field" => SharpFormTextField::make("file_name")
            ]
        ]]));

        $this->assertDatabaseHas("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "test.jpg",
        ]);
    }

    /** @test */
    function we_can_update_a_morphMany_related()
    {
        $person = Person::create(["name" => "John Wayne"]);
        $person->pictures()->create([
            "file" => "old.jpg"
        ]);

        $updater = new MorphManyRelationUpdater();

        $updater->update($person, "pictures", $this->formatData([[
            "id" => [
                "value" => $person->pictures->first()->id, "valuator" => null, "field" => null
            ], "file" => [
                "value" => "test.jpg", "valuator" => null, "field" => SharpFormTextField::make("file_name")
            ]
        ]]));

        $this->assertDatabaseHas("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "test.jpg",
        ]);

        $this->assertDatabaseMissing("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "old.jpg",
        ]);
    }
}