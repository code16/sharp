<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Relationships;

use Code16\Sharp\Form\Eloquent\Relationships\MorphToManyRelationUpdater;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MorphToManyRelationUpdaterTest extends SharpFormEloquentBaseTest
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('taggable_id');
            $table->string('taggable_type');
        });
    }

    /** @test */
    public function we_can_update_a_morphToMany_relation()
    {
        $person = TaggablePerson::create(['name' => 'A']);
        $tag = Tag::create(['name' => 'A']);

        $updater = new MorphToManyRelationUpdater();

        $updater->update($person, 'tags', [
            ['id'=>$tag->id],
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id'        => $tag->id,
            'taggable_id'   => $person->id,
            'taggable_type' => TaggablePerson::class,
        ]);
    }

    /** @test */
    public function we_can_update_an_existing_morphToMany_relation()
    {
        $person = TaggablePerson::create(['name' => 'A']);
        $oldTag = Tag::create(['name' => 'A']);
        $newTag = Tag::create(['name' => 'B']);

        $person->tags()->sync([
            ['id'=>$oldTag->id],
        ]);

        $updater = new MorphToManyRelationUpdater();

        $updater->update($person, 'tags', [
            ['id'=>$newTag->id],
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id'        => $newTag->id,
            'taggable_id'   => $person->id,
            'taggable_type' => TaggablePerson::class,
        ]);

        $this->assertDatabaseMissing('taggables', [
            'tag_id'        => $oldTag->id,
            'taggable_id'   => $person->id,
            'taggable_type' => TaggablePerson::class,
        ]);
    }

    /** @test */
    public function we_can_can_create_a_new_related_item()
    {
        $person = TaggablePerson::create(['name' => 'A']);

        $updater = new MorphToManyRelationUpdater();

        $updater->update($person, 'tags', [
            ['id'=>null, 'name'=>'Z'],
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Z',
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_id'        => Tag::first()->id,
            'taggable_id'   => $person->id,
            'taggable_type' => TaggablePerson::class,
        ]);
    }
}

class Tag extends Model
{
    protected $guarded = [];
}

class TaggablePerson extends Person
{
    protected $table = 'people';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
