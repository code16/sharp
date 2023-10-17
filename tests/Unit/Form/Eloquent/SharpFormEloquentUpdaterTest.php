<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

it('allows to update a simple attribute', function () {
    $person = Person::create(['name' => 'Marie Curry']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie']);

    expect($person->fresh()->name)->toBe('Marie Curie');
})->group('eloquent');

it('allows to store a new instance', function () {
    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data);
        }
    };

    $form->storeInstance(['name' => 'Niehls Bohr']);

    $this->assertDatabaseHas('people', [
        'name' => 'Niehls Bohr',
    ]);
});

it('undeclared fields are ignored', function () {
    $person = Person::create(['name' => 'Marie Curie', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['id' => 1200, 'age' => 38]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore a field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function update($id, array $data)
        {
            return $this
                ->ignore('age')
                ->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie', 'age' => 40]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore multiple field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function update($id, array $data)
        {
            return $this
                ->ignore(['age', 'name'])
                ->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($person->id, ['name' => 'Marie Curie', 'age' => 40]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Niels Bohr',
        'age' => 21,
    ]);
});

it('allows to update a belongsTo attribute', function () {
    $pierre = Person::create(['name' => 'Pierre Curie']);
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormSelectField::make('partner', Person::all()->pluck('name', 'id')->all())
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, ['partner' => $pierre->id]);

    $this->assertDatabaseHas('people', [
        'id' => $marie->id,
        'partner_id' => $pierre->id,
    ]);
});

it('allows to update an hasOne attribute', function () {
    $director = Person::create(['name' => 'Jean']);
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormSelectField::make('director', Person::all()->pluck('name', 'id')->all())
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, ['director' => $director->id]);

    $this->assertDatabaseHas('people', [
        'id' => $director->id,
        'chief_id' => $marie->id,
    ]);
});

it('allows to update an hasMany attribute, creating new instances if needed', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormListField::make('collaborators')
                        ->addItemField(SharpFormTextField::make('name')),
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'collaborators' => [
            ['id' => $collaborator->id, 'name' => 'Paul'],
            ['id' => null, 'name' => 'Jeanne'],
        ],
    ]);

    $this->assertDatabaseHas('people', [
        'id' => $collaborator->id,
        'chief_id' => $marie->id,
        'name' => 'Paul',
    ]);

    $this->assertDatabaseHas('people', [
        'chief_id' => $marie->id,
        'name' => 'Jeanne',
    ]);
});

it('allows to update a belongsToMany attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $colleague = Person::create(['name' => 'Niels Bohr']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTagsField::make('colleagues', Person::all()->pluck('name', 'id')->all()),
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'colleagues' => [
            ['id' => $colleague->id],
        ],
    ]);

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $colleague->id,
    ]);
});

it('allows to create a new related in a belongsToMany attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTagsField::make('colleagues', Person::all()->pluck('name', 'id')->all())
                        ->setCreatable()
                        ->setCreateAttribute('name'),
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'colleagues' => [
            ['id' => null, 'label' => 'Niels Bohr'],
        ],
    ]);

    $niels = Person::where('name', 'Niels Bohr')->first();

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $niels->id,
    ]);
});

it('handles the order attribute in a hasMany relation in both update and creation cases', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id, 'order' => 1]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormListField::make('collaborators')
                        ->addItemField(SharpFormTextField::make('name'))
                        ->setSortable()
                        ->setOrderAttribute('order'),
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'collaborators' => [
            ['id' => null, 'name' => 'Jeanne'],
            ['id' => $collaborator->id, 'name' => 'Paul'],
        ],
    ]);

    $this->assertDatabaseHas('people', [
        'id' => $collaborator->id,
        'chief_id' => $marie->id,
        'name' => 'Paul',
        'order' => 2,
    ]);

    $this->assertDatabaseHas('people', [
        'chief_id' => $marie->id,
        'name' => 'Jeanne',
        'order' => 1,
    ]);
});

it('allows to update a morphOne attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('photo:file'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, ['photo:file' => 'picture']);

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'picture',
    ]);
});

it('allows to update a morphMany attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('pictures')
                    ->addItemField(SharpFormTextField::make('file'))
            );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'pictures' => [
            ['id' => null, 'file' => 'picture-1'],
            ['id' => null, 'file' => 'picture-2'],
        ],
    ]);

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'picture-1',
    ]);

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'picture-2',
    ]);
});

it('handles the relation separator in a belongsTo case', function () {
    $marie = Person::create(['name' => 'Marie Curry', 'age' => 21]);
    $pierre = Person::create(['name' => 'Arthur', 'partner_id' => $marie->id]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('partner:name'))
                ->addField(SharpFormTextField::make('partner:age'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($pierre->id, [
        'partner:name' => 'Marie Curie',
        'partner:age' => 42,
    ]);

    $this->assertDatabaseHas('people', [
        'id' => $marie->id,
        'name' => 'Marie Curie',
        'age' => 42,
    ]);
});

it('handles the relation separator in a hasOne case', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $director = Person::create(['name' => 'Arthur', 'age' => 6, 'chief_id' => $marie->id]);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('director:name'))
                ->addField(SharpFormTextField::make('director:age'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'director:name' => 'Jean',
        'director:age' => 26,
    ]);

    $this->assertDatabaseHas('people', [
        'id' => $director->id,
        'name' => 'Jean',
        'age' => 26,
    ]);
});

it('handles the relation separator in a hasOne creation case', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('director:name'))
                ->addField(SharpFormTextField::make('director:age'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->updateInstance($marie->id, [
        'director:name' => 'Jean',
        'director:age' => 26,
    ]);

    $director = Person::latest('id')->first();

    expect($director)->chief_id->toBe($marie->id)
        ->name->toBe('Jean')
        ->age->toBe(26);
});