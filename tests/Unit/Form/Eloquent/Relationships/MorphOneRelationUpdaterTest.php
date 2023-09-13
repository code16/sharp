<?php

use Code16\Sharp\Form\Eloquent\Relationships\MorphOneRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Fixtures\Picture;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;

it('allows to create a morphOne related', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new MorphOneRelationUpdater();

    $updater->update($marie, 'photo:file', 'test.jpg');

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'test.jpg',
    ]);
})->group('eloquent');

it('allows to update a morphOne related', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $marie->photo()->create(['file' => 'old.jpg']);

    $updater = new MorphOneRelationUpdater();

    $updater->update($marie, 'photo:file', 'test.jpg');

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'test.jpg',
    ]);
})->group('eloquent');

it('ignores a morphOne related if null', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new MorphOneRelationUpdater();

    $updater->update($marie, 'photo:file', null);

    $this->assertCount(0, Picture::all());
})->group('eloquent');
