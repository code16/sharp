<?php

use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows to update a simple attribute', function () {
    $person = Person::create(['name' => 'Marie Curry']);

    $form = new class() extends FakeSharpForm
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

    $form->update($person->id, $form->formatAndValidateRequestData(['name' => 'Marie Curie']));

    expect($person->fresh()->name)->toBe('Marie Curie');
})->group('eloquent');

it('allows to store a new instance', function () {
    $form = new class() extends FakeSharpForm
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

    $form->store($form->formatAndValidateRequestData(['name' => 'Niehls Bohr']));

    $this->assertDatabaseHas('people', [
        'name' => 'Niehls Bohr',
    ]);
});

it('ignores undeclared fields', function () {
    $person = Person::create(['name' => 'Marie Curie', 'age' => 21]);

    $form = new class() extends FakeSharpForm
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

    $form->update($person->id, $form->formatAndValidateRequestData(['id' => 1200, 'age' => 38]));

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('ignores SharpFormHtmlField fields', function () {
    $person = Person::create(['name' => 'Marie Curie', 'age' => 21]);

    $form = new class() extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormHtmlField::make('name')->setTemplate('HTML'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->update($person->id, $form->formatAndValidateRequestData(['name' => 'HTML']));

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore a field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class() extends FakeSharpForm
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

    $form->update($person->id, $form->formatAndValidateRequestData(['name' => 'Marie Curie', 'age' => 40]));

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Marie Curie',
        'age' => 21,
    ]);
});

it('allows to manually ignore multiple field', function () {
    $person = Person::create(['name' => 'Niels Bohr', 'age' => 21]);

    $form = new class() extends FakeSharpForm
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

    $form->update($person->id, $form->formatAndValidateRequestData(['name' => 'Marie Curie', 'age' => 40]));

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Niels Bohr',
        'age' => 21,
    ]);
});

it('allows to have a field with the same name of a model method but not a relation', function () {
    $person = Person::create(['name' => 'Marie Curry']);

    $form = new class() extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('unrelated'));
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    $form->update($person->id, $form->formatAndValidateRequestData(['unrelated' => 'Marie Curie']));

    expect($person->fresh()->unrelated)->toBe('Marie Curie');
});

it('allows to update a belongsTo attribute', function () {
    $pierre = Person::create(['name' => 'Pierre Curie']);
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
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

    $form->update($marie->id, $form->formatAndValidateRequestData(['partner' => $pierre->id]));

    $this->assertDatabaseHas('people', [
        'id' => $marie->id,
        'partner_id' => $pierre->id,
    ]);
});

it('allows to update an hasOne attribute', function () {
    $director = Person::create(['name' => 'Jean']);
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
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

    $form->update($marie->id, $form->formatAndValidateRequestData(['director' => $director->id]));

    $this->assertDatabaseHas('people', [
        'id' => $director->id,
        'chief_id' => $marie->id,
    ]);
});

it('allows to update an hasMany attribute, creating new instances if needed', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id]);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'collaborators' => [
                ['id' => $collaborator->id, 'name' => 'Paul'],
                ['id' => null, 'name' => 'Jeanne'],
            ],
        ])
    );

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

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'colleagues' => [
                ['id' => $colleague->id],
            ],
        ])
    );

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $colleague->id,
    ]);
});

it('allows to create a new related in a belongsToMany attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'colleagues' => [
                ['id' => null, 'label' => 'Niels Bohr'],
            ],
        ])
    );

    $niels = Person::where('name', 'Niels Bohr')->first();

    $this->assertDatabaseHas('colleagues', [
        'person1_id' => $marie->id,
        'person2_id' => $niels->id,
    ]);
});

it('handles the order attribute in a hasMany relation in both update and creation cases', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $collaborator = Person::create(['name' => 'Arthur', 'chief_id' => $marie->id, 'order' => 1]);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'collaborators' => [
                ['id' => null, 'name' => 'Jeanne'],
                ['id' => $collaborator->id, 'name' => 'Paul'],
            ],
        ])
    );

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

    $form = new class() extends FakeSharpForm
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

    $form->update($marie->id, $form->formatAndValidateRequestData(['photo:file' => 'picture']));

    $this->assertDatabaseHas('pictures', [
        'picturable_type' => Person::class,
        'picturable_id' => $marie->id,
        'file' => 'picture',
    ]);
});

it('allows to update a morphMany attribute', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'pictures' => [
                ['id' => null, 'file' => 'picture-1'],
                ['id' => null, 'file' => 'picture-2'],
            ],
        ])
    );

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

it('handles the {id} placeholder of uploads in update case', function () {
    Storage::fake('local');
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('name')
                )
                ->addField(
                    SharpFormUploadField::make('upload')
                        ->setStorageBasePath('test/{id}')
                        ->setStorageDisk('local')
                );
        }

        public function update($id, array $data)
        {
            return $this->save(Person::findOrFail($id), $data);
        }
    };

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $savedCount = 0;

    Person::saved(function () use (&$savedCount) {
        $savedCount++;
    });

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'upload' => [
                'name' => '/image.jpg',
                'uploaded' => true,
            ],
        ])
    );

    $this->assertEquals(1, $savedCount);

    $this->assertDatabaseHas('sharp_upload_models', [
        'model_id' => $marie->id,
        'file_name' => 'test/'.$marie->id.'/image.jpg',
    ]);
});

it('handles the {id} placeholder of uploads in create case', function () {
    Storage::fake('local');

    $form = new class() extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('name')
                )
                ->addField(
                    SharpFormUploadField::make('upload')
                        ->setStorageBasePath('test/{id}')
                        ->setStorageDisk('local')
                );
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data)->id;
        }
    };

    UploadedFile::fake()
        ->image('image-2.jpg')
        ->storeAs('/tmp', 'image-2.jpg', ['disk' => 'local']);

    $form->update(
        null,
        $form->formatAndValidateRequestData([
            'name' => 'Pierre Curie',
            'upload' => [
                'name' => '/image-2.jpg',
                'uploaded' => true,
            ],
        ])
    );

    $pierre = Person::where('name', 'Pierre Curie')->first();

    $this->assertDatabaseHas('sharp_upload_models', [
        'model_id' => $pierre->id,
        'file_name' => 'test/'.$pierre->id.'/image-2.jpg',
    ]);
});

it('handles the {id} placeholder of Editor’s embedded uploads in create case', function () {
    Storage::fake('local');

    $form = new class() extends FakeSharpForm
    {
        use WithSharpFormEloquentUpdater;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('name')
                )
                ->addField(
                    SharpFormEditorField::make('bio')
                        ->allowUploads(
                            SharpFormEditorUpload::make()
                                ->setStorageBasePath('test/{id}')
                                ->setStorageDisk('local')
                        )
                );
        }

        public function update($id, array $data)
        {
            return $this->save(new Person(), $data)->id;
        }
    };

    UploadedFile::fake()
        ->create('my-file.pdf')
        ->storeAs('/tmp', 'my-file.pdf', ['disk' => 'local']);

    $form->update(
        null,
        $form->formatAndValidateRequestData([
            'name' => 'Pierre Curie',
            'bio' => [
                'text' => '<p>my editor</p><x-sharp-file data-key="0"></x-sharp-file>',
                'uploads' => [
                    [
                        'file' => [
                            'name' => 'my-file.pdf',
                            'uploaded' => true,
                        ],
                    ],
                ],
            ],
        ])
    );

    $pierre = Person::where('name', 'Pierre Curie')->first();

    expect($pierre->bio)
        ->toBe(sprintf('<p>my editor</p><x-sharp-file file="%s"></x-sharp-file>', e(json_encode([
            'file_name' => 'test/1/my-file.pdf',
            'size' => 0,
            'mime_type' => 'application/pdf',
            'disk' => 'local',
        ]))));
});

it('handles the relation separator in a belongsTo case', function () {
    $marie = Person::create(['name' => 'Marie Curry', 'age' => 21]);
    $pierre = Person::create(['name' => 'Arthur', 'partner_id' => $marie->id]);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $pierre->id,
        $form->formatAndValidateRequestData([
            'partner:name' => 'Marie Curie',
            'partner:age' => 42,
        ])
    );

    $this->assertDatabaseHas('people', [
        'id' => $marie->id,
        'name' => 'Marie Curie',
        'age' => 42,
    ]);
});

it('handles the relation separator in a hasOne case', function () {
    $marie = Person::create(['name' => 'Marie Curie']);
    $director = Person::create(['name' => 'Arthur', 'age' => 6, 'chief_id' => $marie->id]);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'director:name' => 'Jean',
            'director:age' => 26,
        ])
    );

    $this->assertDatabaseHas('people', [
        'id' => $director->id,
        'name' => 'Jean',
        'age' => 26,
    ]);
});

it('handles the relation separator in a hasOne creation case', function () {
    $marie = Person::create(['name' => 'Marie Curie']);

    $form = new class() extends FakeSharpForm
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

    $form->update(
        $marie->id,
        $form->formatAndValidateRequestData([
            'director:name' => 'Jean',
            'director:age' => 26,
        ])
    );

    $director = Person::latest('id')->first();

    expect($director)->chief_id->toBe($marie->id)
        ->name->toBe('Jean')
        ->age->toBe(26);
});
