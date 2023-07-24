<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\BelongsToRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

class BelongsToRelationUpdaterTest extends SharpEloquentBaseTest
{
    /** @test */
    public function we_can_update_a_belongsTo_relation()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);
        $person = Person::create(['name' => 'John Wayne']);

        $updater = new BelongsToRelationUpdater();

        $updater->update($person, 'mother', $mother->id);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'mother_id' => $mother->id,
        ]);
    }

    /** @test */
    public function we_can_create_a_belongsTo_relation()
    {
        $person = Person::create(['name' => 'John Wayne']);

        $updater = new BelongsToRelationUpdater();
        $updater->update($person, 'mother:name', 'Jane Wayne');

        $this->assertCount(2, Person::all());

        $mother = Person::where('name', 'Jane Wayne')->first();

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'mother_id' => $mother->id,
        ]);
    }

    /** @test */
    public function we_set_default_attributes_when_creating_a_belongsTo_relation()
    {
        $person = PersonWithDefaultAttributes::create(['name' => 'John Wayne']);

        $updater = new BelongsToRelationUpdater();
        $updater->update($person, 'mother:name', 'Jane Wayne');

        $this->assertDatabaseHas('people', [
            'name' => 'Jane Wayne',
            'age' => 60,
        ]);
    }
}

class PersonWithDefaultAttributes extends Person
{
    protected $table = 'people';

    public function getDefaultAttributesFor($attribute)
    {
        if ($attribute == 'mother') {
            return ['age' => 60];
        }

        return [];
    }
}
