<?php

use Code16\Sharp\Form\Eloquent\Relationships\MorphManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

it('allows to create a morphMany related', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new MorphManyRelationUpdater();

    $updater->update($marie, 'pictures', [[
        'id' => null,
        'file' => 'test.jpg',
    ]]);

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'test.jpg',
    ]);
})->group('eloquent');

it('allows to update_a_morphMany_related', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $marie->pictures()->create([
        'file' => 'old.jpg',
    ]);

    $updater = new MorphManyRelationUpdater();

    $updater->update($marie, 'pictures', [[
        'id' => $marie->pictures->first()->id,
        'file' => 'test.jpg',
    ]]);

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'test.jpg',
    ]);

    $this->assertDatabaseMissing('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'old.jpg',
    ]);
})->group('eloquent');
