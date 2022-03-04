<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\HasOneRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;

class HasOneRelationUpdaterTest extends SharpFormEloquentBaseTest
{
    /** @test */
    public function we_can_update_a_hasOne_relation()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);
        $son = Person::create(['name' => 'John Wayne']);

        $updater = new HasOneRelationUpdater();

        $updater->update($mother, 'elderSon', $son->id);

        $this->assertDatabaseHas('people', [
            'id'        => $son->id,
            'mother_id' => $mother->id,
        ]);
    }

    /** @test */
    public function we_can_create_a_hasOne_related()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);

        $updater = new HasOneRelationUpdater();

        $updater->update($mother, 'elderSon:name', 'John Wayne');

        $this->assertDatabaseHas('people', [
            'name'      => 'John Wayne',
            'mother_id' => $mother->id,
        ]);
    }
}
