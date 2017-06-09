<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent;

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
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

        $this->assertNotNull(
            $form->update($person->id, ["name" => "Claire Trevor"])
        );

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "Claire Trevor"
        ]);
    }

    /** @test */
    function undeclared_fields_are_ignored()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($person->id, ["id" => 1200, "job" => "Actor"]);

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "John Wayne"
        ]);
    }

    /** @test */
    function we_can_use_a_closure_as_a_custom_valuator()
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
    function we_can_use_a_class_as_a_custom_valuator()
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

        $this->assertNotNull(
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

        $this->assertNotNull(
            $form->update($mother->id, ["elderSon" => $son->id])
        );

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "mother_id" => $mother->id
        ]);
    }

    /** @test */
    function we_can_update_a_hasMany_attribute()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        $son = Person::create(["name" => "AAA"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertNotNull(
            $form->update($mother->id, [
                "sons" => [
                    ["id" => $son->id, "name" => "John Wayne"],
                    ["id" => null, "name" => "Mary Wayne"],
                ]
            ])
        );

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "mother_id" => $mother->id,
            "name" => "John Wayne"
        ]);

        $this->assertDatabaseHas("people", [
            "mother_id" => $mother->id,
            "name" => "Mary Wayne"
        ]);
    }

    /** @test */
    function we_can_update_a_belongsToMany_attribute()
    {
        $person1 = Person::create(["name" => "John Ford"]);
        $person2 = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertNotNull(
            $form->update($person1->id, [
                "friends" => [
                    ["id" => $person2->id]
                ]
            ])
        );

        $this->assertDatabaseHas("friends", [
            "person1_id" => $person1->id,
            "person2_id" => $person2->id,
        ]);
    }

    /** @test */
    function we_can_create_a_new_related_in_a_belongsToMany_attribute()
    {
        $person1 = Person::create(["name" => "John Ford"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $this->assertNotNull(
            $form->update($person1->id, [
                "friends" => [
                    ["id" => null, "label" => "John Wayne"]
                ]
            ])
        );

        $this->assertDatabaseHas("people", [
            "name" => "John Wayne"
        ]);

        $person2 = Person::where("name", "John Wayne")->first();

        $this->assertDatabaseHas("friends", [
            "person1_id" => $person1->id,
            "person2_id" => $person2->id,
        ]);
    }

    /** @test */
    function we_can_use_a_custom_valuator_in_a_hasMany_relation()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();
        $form->setCustomValuator("sons.name", function($person, $value) {
            return strtoupper($value);
        });
        $form->update($mother->id, [
            "sons" => [
                ["id" => null, "name" => "John Wayne"],
                ["id" => null, "name" => "Mary Wayne"],
            ]
        ]);

        $this->assertDatabaseHas("people", [
            "mother_id" => $mother->id,
            "name" => "JOHN WAYNE"
        ]);

        $this->assertDatabaseHas("people", [
            "mother_id" => $mother->id,
            "name" => "MARY WAYNE"
        ]);
    }

}

class WithSharpFormEloquentUpdaterTestForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    function find($id): array { return []; }
    function update($id, array $data)
    {
        $instance = $id ? Person::findOrFail($id) : new Person;
        return $this->save($instance, $data);
    }
    function delete($id) { return false; }
    function buildFormLayout() {}
    function buildFormFields()
    {
        $peopleList = Person::all()->pluck("name", "id")->all();
        $this->addField(SharpFormTextField::make("name"));
        $this->addField(SharpFormSelectField::make("mother_id", $peopleList));
        $this->addField(SharpFormSelectField::make("mother", $peopleList));
        $this->addField(SharpFormSelectField::make("elderSon", $peopleList));
        $this->addField(
            SharpFormListField::make("sons")
                ->addItemField(SharpFormTextField::make("name"))
        );
        $this->addField(
            SharpFormTagsField::make("friends", $peopleList)
                ->setCreatable()
                ->setCreateAttribute("name")
        );
    }
}

class SharpAttributeUppercaseValuator implements SharpAttributeValuator
{
    function getValue($instance, string $attribute, $value)
    {
        return strtoupper($value);
    }
}