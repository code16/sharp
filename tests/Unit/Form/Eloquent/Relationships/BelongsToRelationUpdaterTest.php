<?php

use Code16\Sharp\Form\Eloquent\Relationships\BelongsToRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;

it('allows to update a belongsTo relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $pierre = Person::create(['name' => 'Pierre Curie']);

    $updater = new BelongsToRelationUpdater();

    $updater->update($pierre, 'partner', $marie->id);

    $this->assertDatabaseHas('people', [
        'id' => $pierre->id,
        'partner_id' => $marie->id,
    ]);
})->group('eloquent');

it('allows to create a belongsTo relation', function () {
    $pierre = Person::create(['name' => 'Pierre Curie']);

    $updater = new BelongsToRelationUpdater();
    $updater->update($pierre, 'partner:name', 'Marie Curie');

    expect(Person::all())->toHaveCount(2);

    $marie = Person::latest('id')->first();

    $this->assertDatabaseHas('people', [
        'id' => $pierre->id,
        'partner_id' => $marie->id,
    ]);
})->group('eloquent');

it('sets default attributes when creating a belongsTo relation', function () {
    $pierre = new class() extends Person
    {
        protected $table = 'people';

        public function getDefaultAttributesFor($attribute)
        {
            return $attribute == 'partner' ? ['age' => 60] : [];
        }
    };

    $updater = new BelongsToRelationUpdater();
    $updater->update($pierre, 'partner:name', 'Marie Curie');

    $this->assertDatabaseHas('people', [
        'name' => 'Marie Curie',
        'age' => 60,
    ]);
})->group('eloquent');
