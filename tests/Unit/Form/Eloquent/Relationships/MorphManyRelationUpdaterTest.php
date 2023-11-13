<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\MorphManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

class MorphManyRelationUpdaterTest extends SharpEloquentBaseTest
{
    /** @test */
    public function we_can_create_a_morphMany_related()
    {
        $person = Person::create(['name' => 'John Wayne']);

        $updater = new MorphManyRelationUpdater();

        $updater->update($person, 'pictures', [[
            'id' => null,
            'file' => 'test.jpg',
        ]]);

        $this->assertDatabaseHas('pictures', [
            'picturable_type' => Person::class,
            'picturable_id' => $person->id,
            'file' => 'test.jpg',
        ]);
    }

    /** @test */
    public function we_can_update_a_morphMany_related()
    {
        $person = Person::create(['name' => 'John Wayne']);
        $person->pictures()->create([
            'file' => 'old.jpg',
        ]);

        $updater = new MorphManyRelationUpdater();

        $updater->update($person, 'pictures', [[
            'id' => $person->pictures->first()->id,
            'file' => 'test.jpg',
        ]]);

        $this->assertDatabaseHas('pictures', [
            'picturable_type' => Person::class,
            'picturable_id' => $person->id,
            'file' => 'test.jpg',
        ]);

        $this->assertDatabaseMissing('pictures', [
            'picturable_type' => Person::class,
            'picturable_id' => $person->id,
            'file' => 'old.jpg',
        ]);
    }
}
