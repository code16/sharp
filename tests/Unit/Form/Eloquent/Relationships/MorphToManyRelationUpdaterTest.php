<?php

use Code16\Sharp\Form\Eloquent\Relationships\MorphToManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Fixtures\Tag;

it('allows to update a morphToMany relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $tag = Tag::create(['name' => 'Physicist']);

    $updater = new MorphToManyRelationUpdater();

    $updater->update($marie, 'tags', [
        ['id' => $tag->id],
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $tag->id,
        'taggable_id' => $marie->id,
        'taggable_type' => Person::class,
    ]);
});

it('allows to update_an_existing_morphToMany_relation', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $oldTag = Tag::create(['name' => 'Chemist']);
    $newTag = Tag::create(['name' => 'Physicist']);

    $marie->tags()->attach([$oldTag->id]);

    $updater = new MorphToManyRelationUpdater();

    $updater->update($marie, 'tags', [
        ['id' => $newTag->id],
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => $newTag->id,
        'taggable_id' => $marie->id,
        'taggable_type' => Person::class,
    ]);

    $this->assertDatabaseMissing('taggables', [
        'tag_id' => $oldTag->id,
        'taggable_id' => $marie->id,
        'taggable_type' => Person::class,
    ]);
});

it('allows to create a new related item', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $updater = new MorphToManyRelationUpdater();

    $updater->update($marie, 'tags', [
        ['id' => null, 'name' => 'Physicist'],
    ]);

    $this->assertDatabaseHas('tags', [
        'name' => 'Physicist',
    ]);

    $this->assertDatabaseHas('taggables', [
        'tag_id' => Tag::first()->id,
        'taggable_id' => $marie->id,
        'taggable_type' => Person::class,
    ]);
});
