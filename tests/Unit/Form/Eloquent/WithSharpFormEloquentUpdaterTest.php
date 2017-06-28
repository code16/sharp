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
    function we_can_manually_ignore_a_field()
    {
        $person = Person::create(["name" => "A", "age" => 21]);

        $form = new class extends WithSharpFormEloquentUpdaterTestForm {
            function update($id, array $data)
            {
                return $this->ignore("age")
                    ->save(Person::findOrFail($id), $data);
            }
        };

        $form->update($person->id, ["name" => "John Wayne", "age" => 40]);

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "John Wayne",
            "age" => 21
        ]);
    }

    /** @test */
    function we_can_manually_ignore_multiple_fields()
    {
        $person = Person::create(["name" => "A", "age" => 21]);

        $form = new class extends WithSharpFormEloquentUpdaterTestForm {
            function update($id, array $data)
            {
                return $this->ignore(["name", "age"])
                    ->save(Person::findOrFail($id), $data);
            }
        };

        $form->update($person->id, ["name" => "John Wayne", "age" => 40]);

        $this->assertDatabaseHas("people", [
            "id" => $person->id,
            "name" => "A",
            "age" => 21
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
        $son = Person::create(["name" => "AAA", "mother_id" => $mother->id]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($mother->id, [
            "sons" => [
                ["id" => $son->id, "name" => "John Wayne"],
                ["id" => null, "name" => "Mary Wayne"],
            ]
        ]);

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

    /** @test */
    function we_handle_the_order_attribute_in_a_hasMany_relation_in_a_creation_case()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();
        $form->update($mother->id, [
            "sons" => [
                ["id" => null, "name" => "John Wayne"],
                ["id" => null, "name" => "Mary Wayne"],
            ]
        ]);

        $this->assertDatabaseHas("people", [
            "mother_id" => $mother->id,
            "name" => "John Wayne",
            "order" => 1
        ]);

        $this->assertDatabaseHas("people", [
            "mother_id" => $mother->id,
            "name" => "Mary Wayne",
            "order" => 2
        ]);
    }

    /** @test */
    function we_handle_the_order_attribute_in_a_hasMany_relation_in_an_update_case()
    {
        $mother = Person::create(["name" => "A"]);
        $son = Person::create(["name" => "B", "order" => 30, "mother_id"=>$mother->id]);
        $daughter = Person::create(["name" => "C", "order" => 50, "mother_id"=>$mother->id]);

        $form = new WithSharpFormEloquentUpdaterTestForm();
        $form->update($mother->id, [
            "sons" => [
                ["id" => $daughter->id, "name" => "C"],
                ["id" => $son->id, "name" => "B"],
            ]
        ]);

        $this->assertDatabaseHas("people", [
            "id" => $daughter->id,
            "order" => 1
        ]);

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "order" => 2
        ]);

    }

    /** @test */
    function we_can_update_a_morphOne_attribute()
    {
        $person = Person::create(["name" => "John Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($person->id, ["picture:file" => "picture"]);

        $this->assertDatabaseHas("pictures", [
            "picturable_type" => Person::class,
            "picturable_id" => $person->id,
            "file" => "picture",
        ]);
    }

    /** @test */
    function the_relation_separator_is_properly_handled_in_a_belongsTo_case()
    {
        $mother = Person::create(["name" => "AAA"]);
        $son = Person::create(["name" => "John Wayne", "mother_id" => $mother->id]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($son->id, [
            "mother:name" => "Jane Wayne",
            "mother:age" => 92
        ]);

        $this->assertDatabaseHas("people", [
            "id" => $mother->id,
            "age" => 92,
            "name" => "Jane Wayne"
        ]);
    }

    /** @test */
    function the_relation_separator_is_properly_handled_in_a_hasOne_case()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);
        $son = Person::create(["name" => "AAA", "mother_id" => $mother->id]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($mother->id, [
            "elderSon:name" => "John Wayne",
            "elderSon:age" => 52
        ]);

        $this->assertDatabaseHas("people", [
            "id" => $son->id,
            "name" => "John Wayne",
            "age" => 52,
            "mother_id" => $mother->id
        ]);
    }

    /** @test */
    function the_relation_separator_is_properly_handled_in_a_hasOne_creation_case()
    {
        $mother = Person::create(["name" => "Jane Wayne"]);

        $form = new WithSharpFormEloquentUpdaterTestForm();

        $form->update($mother->id, [
            "elderSon:name" => "John Wayne",
            "elderSon:age" => 52
        ]);

        $this->assertDatabaseHas("people", [
            "name" => "John Wayne",
            "age" => 52,
            "mother_id" => $mother->id
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
        $this->addField(SharpFormTextField::make("age"));
        $this->addField(SharpFormSelectField::make("mother_id", $peopleList));
        $this->addField(SharpFormSelectField::make("mother", $peopleList));
        $this->addField(SharpFormSelectField::make("elderSon", $peopleList));
        $this->addField(
            SharpFormListField::make("sons")
                ->setOrderAttribute("order")
                ->setSortable()
                ->addItemField(SharpFormTextField::make("name"))
        );
        $this->addField(
            SharpFormTagsField::make("friends", $peopleList)
                ->setCreatable()
                ->setCreateAttribute("name")
        );
        $this->addField(SharpFormTextField::make("mother:name"));
        $this->addField(SharpFormTextField::make("mother:age"));
        $this->addField(SharpFormTextField::make("elderSon:name"));
        $this->addField(SharpFormTextField::make("elderSon:age"));
        $this->addField(SharpFormTextField::make("picture:file"));
    }
}

class SharpAttributeUppercaseValuator implements SharpAttributeValuator
{
    function getValue($instance, string $attribute, $value)
    {
        return strtoupper($value);
    }
}