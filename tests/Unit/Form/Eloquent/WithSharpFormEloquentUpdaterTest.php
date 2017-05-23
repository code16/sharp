<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent;

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\Transformers\SharpAttributeValuator;
use Code16\Sharp\Tests\Fixtures\Person;

class WithSharpFormEloquentUpdaterTest extends SharpFormEloquentBaseTest
{
    /** @test */
    function we_can_update_a_simple_attribute()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertTrue(
            $form->update($person->id, ["name" => "Claire Trevor"])
        );

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "Claire Trevor"
        ]);
    }

    /** @test */
    function we_can_use_a_closure_as_a_custom_updater()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();
        $form->setCustomValuator("name", function($person, $value) {
            return strtoupper($value);
        });
        $form->update($person->id, ["name" => "John Richard Wayne"]);

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "JOHN RICHARD WAYNE"
        ]);
    }

    /** @test */
    function we_can_use_a_class_as_a_custom_updater()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();
        $form->setCustomValuator("name", SharpAttributeUppercaseValuator::class);
        $form->update($person->id, ["name" => "John Richard Wayne"]);

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "JOHN RICHARD WAYNE"
        ]);
    }

    /** @test */
    function we_can_update_a_belongsTo_attribute()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertTrue(
            $form->update($person->id, ["mother" => $mother->id])
        );

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "mother_id" => $mother->id
        ]);
    }

    /** @test */
    function we_can_update_a_hasOne_attribute()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        $son = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertTrue(
            $form->update($mother->id, ["elderSon" => $son->id])
        );

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "mother_id" => $mother->id
        ]);
    }
}

class WithSharpFormEloquentUpdaterTestForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function find($id): array { return []; }
    function update($id, array $data): bool
    {
        $instance = $id ? Person::findOrFail($id) : new Person;
        return $this->save($instance, $data);
    }
    function delete($id): bool { return false; }
    function buildFormLayout() {}
    function buildFormFields()
    {
        $peopleList = Person::all()->pluck("name", "id")->all();
        $this->addField(SharpFormTextField::make("name"));
        $this->addField(SharpFormSelectField::make("mother_id", $peopleList));
        $this->addField(SharpFormSelectField::make("mother", $peopleList));
        $this->addField(SharpFormSelectField::make("elderSon", $peopleList));
    }
}

class SharpAttributeUppercaseValuator implements SharpAttributeValuator
{
    function getValue($instance, string $attribute, $value)
    {
        return strtoupper($value);
    }
}