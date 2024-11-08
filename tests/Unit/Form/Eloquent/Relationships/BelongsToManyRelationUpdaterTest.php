<?php

use Code16\Sharp\Form\Eloquent\Relationships\BelongsToManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;

it('allows to update a belongsToMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $colleague = Person::create(['name' => 'Niels Bohr']);

    $updater = new BelongsToManyRelationUpdater();

    $updater->update($marie, 'colleagues', [
        ['id' => $colleague->id],
    ]);

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $colleague->id,
    ]);

    expect($marie->fresh()->colleagues)->toHaveCount(1)
        ->and(Person::all())->toHaveCount(2);
})->group('eloquent');

it('allows to update an existing belongsToMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $niels = Person::create(['name' => 'Niels Bohr']);
    $albert = Person::create(['name' => 'Albert Einstein']);

    $marie->colleagues()->attach([$niels->id]);

    $updater = new BelongsToManyRelationUpdater();

    $updater->update($marie, 'colleagues', [
        ['id' => $albert->id],
    ]);

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $albert->id,
    ]);

    $this->assertDatabaseMissing('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $niels->id,
    ]);
})->group('eloquent');

it('allows to can_create a new related item', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new BelongsToManyRelationUpdater();

    $updater->update($marie, 'colleagues', [
        ['id' => null, 'name' => 'Niels Bohr'],
    ]);

    $this->assertDatabaseHas('people', [
        'name' => 'Niels Bohr',
    ]);

    $niels = Person::latest('id')->first();

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $niels->id,
    ]);
})->group('eloquent');

it('handles order in a belongsToMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $niels = Person::create(['name' => 'Niels Bohr']);
    $albert = Person::create(['name' => 'Albert Einstein']);

    $marie->colleagues()->sync([
        ['id' => $niels->id, 'order' => 100],
        ['id' => $albert->id, 'order' => 100],
    ]);

    $updater = new BelongsToManyRelationUpdater();
    $updater->update(
        $marie,
        'colleagues',
        [['id' => $albert->id], ['id' => $niels->id]],
        ['orderAttribute' => 'order']
    );

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $albert->id,
        'order' => 1,
    ]);

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $niels->id,
        'order' => 2,
    ]);
})->group('eloquent');