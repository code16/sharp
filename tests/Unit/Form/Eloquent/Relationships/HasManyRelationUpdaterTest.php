<?php

use Code16\Sharp\Form\Eloquent\Relationships\HasManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships\Fixtures\PersonWithFixedId;

it('allows to update a hasMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator1 = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id]);

    $updater = new HasManyRelationUpdater();

    $updater->update($marie, 'collaborators', [[
        'id' => $collaborator1->id,
        'name' => 'Arthur',
    ]]);

    $this->assertDatabaseHas('people', [
        'id' => $collaborator1->id,
        'chief_id' => $marie->id,
        'name' => 'Arthur',
    ]);
})->group('eloquent');

it('allows to create a new related item in a hasMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new HasManyRelationUpdater();

    $updater->update($marie, 'collaborators', [[
        'id' => null,
        'name' => 'Arthur',
    ]]);

    $this->assertDatabaseHas('people', [
        'chief_id' => $marie->id,
        'name' => 'Arthur',
    ]);
})->group('eloquent');

it('does not update the id attribute when updating a related item in a hasMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    (new HasManyRelationUpdater())
        ->update($marie, 'collaborators', [[
            'id' => 'ABC', // Set an invalid id here to ensure it will be unset
            'name' => 'Arthur',
        ]]);

    $arthur = Person::where('chief_id', $marie->id)->where('name', 'Arthur')->first();
    expect($arthur->id)->not->toBe('ABC');
})->group('eloquent');

it('updates the id attribute when updating a related item in a hasMany relation in a non incrementing id case', function () {
    $marie = PersonWithFixedId::create(['name' => 'Marie Curie']);

    (new HasManyRelationUpdater())
        ->update($marie, 'collaborators', [[
            'id' => 123,
            'name' => 'Arthur',
        ]]);

    $this->assertDatabaseHas('people', [
        'chief_id' => $marie->id,
        'id' => 123,
    ]);
})->group('eloquent');

it('calls the optional getDefaultAttributesFor method on an item creation', function () {
    $marie = new class() extends Person
    {
        protected $table = 'people';

        public function getDefaultAttributesFor($attribute)
        {
            return $attribute == 'collaborators' ? ['age' => 18] : [];
        }
    };
    $marie->name = 'Marie Curie';
    $marie->save();

    $updater = new HasManyRelationUpdater();

    $updater->update($marie, 'collaborators', [[
        'id' => null, 'name' => 'Arthur',
    ]]);

    $this->assertDatabaseHas('people', [
        'chief_id' => $marie->id,
        'name' => 'Arthur',
        'age' => 18,
    ]);
})->group('eloquent');

it('allows to delete an existing related item in a hasMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator1 = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id]);
    $collaborator2 = Person::create(['name' => 'Jeanne', 'chief_id' => $marie->id]);

    $updater = new HasManyRelationUpdater();

    $updater->update($marie, 'collaborators', [[
        'id' => $collaborator1->id,
        'name' => 'Arthur',
    ]]);

    $this->assertDatabaseMissing('people', [
        'id' => $collaborator2->id,
        'chief_id' => $marie->id,
    ]);
})->group('eloquent');
