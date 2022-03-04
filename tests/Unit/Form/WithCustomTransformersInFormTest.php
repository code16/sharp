<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WithCustomTransformersInFormTest extends SharpFormEloquentBaseTest
{
    /** @test */
    public function we_can_retrieve_an_array_version_of_a_model()
    {
        $person = Person::create([
            'name' => 'John Wayne',
        ]);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'name' => 'John Wayne',
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_can_retrieve_an_array_version_of_a_stdclass()
    {
        $person = Person::create([
            'name' => 'John Wayne',
        ]);
        $form = new class() extends WithCustomTransformersTestForm {
            public function find($id): array
            {
                return $this->transform(
                    DB::table((new Person())->getTable())->where(['id'=>$id])->first()
                );
            }
        };
        $this->assertArraySubset(
            [
                'name' => 'John Wayne',
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function belongsTo_is_handled()
    {
        $mother = Person::create([
            'name' => 'Jane Wayne',
        ]);

        $person = Person::create([
            'name'      => 'John Wayne',
            'mother_id' => $mother->id,
        ]);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'mother_id' => $person->mother_id,
                'mother'    => ['id' => $mother->id, 'name' => $mother->name],
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function hasOne_is_handled()
    {
        $mother = Person::create([
            'name' => 'Jane Wayne',
        ]);

        $son = Person::create([
            'name'      => 'John Wayne',
            'mother_id' => $mother->id,
        ]);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'elder_son' => ['id' => $son->id, 'name' => $son->name],
            ],
            $form->find($mother->id)
        );
    }

    /** @test */
    public function hasMany_is_handled()
    {
        $mother = Person::create([
            'name' => 'Jane Wayne',
        ]);

        $son1 = Person::create([
            'name'      => 'John Wayne',
            'mother_id' => $mother->id,
        ]);

        $son2 = Person::create([
            'name'      => 'Bill Wayne',
            'mother_id' => $mother->id,
        ]);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'sons' => [
                    ['id' => $son1->id, 'name' => $son1->name],
                    ['id' => $son2->id, 'name' => $son2->name],
                ],
            ],
            $form->find($mother->id)
        );
    }

    /** @test */
    public function belongsToMany_is_handled()
    {
        $person1 = Person::create([
            'name' => 'John Wayne',
        ]);

        $person2 = Person::create([
            'name' => 'Louise Brooks',
        ]);

        $person3 = Person::create([
            'name' => 'Claire Trevor',
        ]);

        $person1->friends()->attach([
            $person2->id, $person3->id,
        ]);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'friends' => [
                    ['id' => $person2->id, 'name' => $person2->name],
                    ['id' => $person3->id, 'name' => $person3->name],
                ],
            ],
            $form->find($person1->id)
        );
    }

    /** @test */
    public function morphOne_is_handled()
    {
        $person = Person::create(['name' => 'John Wayne']);
        $person->picture()->create(['file' => 'test.jpg']);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'picture' => ['file' => 'test.jpg'],
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function morphMany_is_handled()
    {
        $person = Person::create(['name' => 'John Wayne']);
        $person->pictures()->create(['file' => 'test.jpg']);

        $form = new WithCustomTransformersTestForm();

        $this->assertArraySubset(
            [
                'pictures' => [['file' => 'test.jpg']],
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_handle_the_relation_separator()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);
        $son = Person::create(['name' => 'AAA', 'mother_id' => $mother->id]);
        $person = Person::create(['name' => 'BBB']);

        $form = new class() extends WithCustomTransformersTestForm {
            public function buildFormFields(): void
            {
                $this->addField(SharpFormTextField::make('mother:name'));
            }
        };

        $this->assertArraySubset(
            [
                'mother:name' => 'Jane Wayne',
            ],
            $form->find($son->id)
        );

        $this->assertArraySubset(
            [
                'mother:name' => null,
            ],
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_can_use_a_closure_as_a_custom_transformer()
    {
        $person = Person::create([
            'name' => 'John Wayne',
        ]);

        $form = new WithCustomTransformersTestForm();
        $form->setCustomTransformer('name', function ($name) {
            return strtoupper($name);
        });

        $this->assertArraySubset(
            ['name' => 'JOHN WAYNE'],
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_can_use_a_class_as_a_custom_transformer()
    {
        $person = Person::create([
            'name' => 'John Wayne',
        ]);

        $form = new WithCustomTransformersTestForm();
        $form->setCustomTransformer('name', SharpAttributeUppercaseTransformer::class);

        $this->assertArraySubset(
            ['name' => 'JOHN WAYNE'],
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_can_use_applyIfAttributeIsMissing_in_a_custom_transformer()
    {
        $person = Person::create([
            'name' => 'John Wayne',
        ]);

        $form = new WithCustomTransformersTestForm();
        $form->setCustomTransformer('slug_name', SharpAttributeSlugNameWithoutMissingTransformer::class);

        $this->assertArrayNotHasKey(
            'slug_name',
            $form->find($person->id)
        );

        $form->setCustomTransformer('slug_name', SharpAttributeSlugNameWithMissingTransformer::class);

        $this->assertArrayHasKey(
            'slug_name',
            $form->find($person->id)
        );
    }

    /** @test */
    public function we_can_use_add_a_custom_transformer_to_a_field_in_a_list()
    {
        $mother = Person::create(['name' => 'Jane Wayne']);
        Person::create(['name' => 'aaa', 'mother_id' => $mother->id]);
        Person::create(['name' => 'bbb', 'mother_id' => $mother->id]);

        $form = new class() extends WithCustomTransformersTestForm {
            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormListField::make('sons')
                    ->addItemField(SharpFormTextField::make('name'))
                );
            }
        };

        $form->setCustomTransformer('sons[name]', function ($name) {
            return strtoupper($name);
        });

        $this->assertArraySubset(
            ['name' => 'AAA'],
            $form->find($mother->id)['sons'][0]
        );

        $this->assertArraySubset(
            ['name' => 'BBB'],
            $form->find($mother->id)['sons'][1]
        );
    }

    /** @test */
    public function we_can_apply_a_custom_transformer_to_a_field_in_a_list_on_an_array_model()
    {
        $form = new class() extends WithCustomTransformersTestForm {
            public function find($id): array
            {
                return $this->transform([
                    'name' => 'Jane Wayne',
                    'sons' => [
                        [
                            'id'   => 12,
                            'name' => 'aaa',
                        ],
                    ],
                ]);
            }

            public function buildFormFields(): void
            {
                $this->addField(
                    SharpFormListField::make('sons')
                    ->addItemField(SharpFormTextField::make('name'))
                );
            }
        };

        $form
            ->setCustomTransformer('sons[name]', function ($name) {
                return strtoupper($name);
            });

        $this->assertArraySubset(
            ['name' => 'AAA'],
            $form->find(null)['sons'][0]
        );
    }
}

class WithCustomTransformersTestForm extends SharpForm
{
    use WithCustomTransformers;

    public function find($id): array
    {
        return $this->transform(
            Person::whereId($id)
                ->with(['sons', 'elderSon', 'mother', 'friends', 'picture', 'pictures'])
                ->firstOrFail()
        );
    }

    public function update($id, array $data): bool
    {
        return false;
    }

    public function delete($id): void
    {
    }

    public function buildFormLayout(): void
    {
    }

    public function buildFormFields(): void
    {
    }
}

class SharpAttributeUppercaseTransformer implements SharpAttributeTransformer
{
    public function apply($value, $instance = null, $attribute = null)
    {
        return strtoupper($value);
    }
}

class SharpAttributeSlugNameWithMissingTransformer implements SharpAttributeTransformer
{
    public function apply($value, $instance = null, $attribute = null)
    {
        return Str::slug($instance->name);
    }
}

class SharpAttributeSlugNameWithoutMissingTransformer extends SharpAttributeSlugNameWithMissingTransformer
{
    public function applyIfAttributeIsMissing(): bool
    {
        return false;
    }
}
