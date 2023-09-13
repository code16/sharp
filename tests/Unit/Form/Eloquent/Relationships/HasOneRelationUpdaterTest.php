<?php

use Code16\Sharp\Form\Eloquent\Relationships\HasOneRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

it('allows to update a hasOne relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $director = Person::create(['name' => 'Arthur']);

    $updater = new HasOneRelationUpdater();

    $updater->update($marie, 'director', $director->id);

    $this->assertDatabaseHas('people', [
        'id' => $director->id,
        'chief_id' => $marie->id,
    ]);
})->group('eloquent');

it('allows to create a hasOne related', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new HasOneRelationUpdater();

    $updater->update($marie, 'director:name', 'Jeanne');

    $this->assertDatabaseHas('people', [
        'name' => 'Jeanne',
        'chief_id' => $marie->id,
    ]);
})->group('eloquent');