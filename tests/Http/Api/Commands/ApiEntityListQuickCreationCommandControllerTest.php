<?php

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('allows to call a quick creation command with the standard form', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }
    });

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
        )
        ->assertOk()
        ->assertJson([
            'fields' => [
                'name' => [
                    'key' => 'name',
                ],
                'job' => [
                    'key' => 'job',
                ],
            ],
        ]);
});

it('allows to call a quick creation command with form layout', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });
    
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('job'))
                ->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormListField::make('collaborators')
                    ->addItemField(SharpFormTextField::make('name'))
                );
        }
        
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(6, function(FormLayoutColumn $column) {
                $column->withField('name');
            })
            ->addColumn(6, function(FormLayoutColumn $column) {
                $column->withField('job')
                    ->withListField('collaborators', function(FormLayoutColumn $column) {
                        $column->withField('name');
                    });
            });
        }
    });
    
    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
        )
        ->assertOk()
        ->assertJson([
            'layout' => [
                'tabbed' => false,
                'tabs' => [
                    [
                        'columns' => [
                            [
                                'fields' => [
                                    [
                                        ['key' => 'name', 'size' => 12],
                                    ],
                                    [
                                        ['key' => 'job', 'size' => 12],
                                    ],
                                    [
                                        [
                                            'key' => 'collaborators',
                                            'size' => 12,
                                            'item' => [
                                                [
                                                    ['key' => 'name', 'size' => 12],
                                                ],
                                            ]
                                        ],
                                    ],
                                ],
                                'size' => 12,
                            ],
                        ],
                        'title' => 'one',
                    ],
                ],
            ],
            'fields' => [
                'name' => [
                    'key' => 'name',
                ],
                'job' => [
                    'key' => 'job',
                ],
                'collaborators' => [
                    'key' => 'collaborators',
                ],
            ],
        ]);
});


it('allows to call a quick creation command with custom form fields', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm(['name']);
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }
    });

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
        )
        ->assertOk()
        ->assertJsonCount(1, 'fields');
});

it('fails when calling a quick creation command on a not configured list', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void {}
    });

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
        )
        ->assertStatus(403);
});

it('allows to post a quick creation command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }

        public function update($id, array $data)
        {
            expect($data)->toBe([
                'name' => 'Marie Curie',
                'job' => 'Scientist',
            ]);

            return 1;
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
            ['data' => ['name' => 'Marie Curie', 'job' => 'Scientist']],
        )
        ->assertOk()
        ->assertJson(['action' => 'reload']);
});

it('validates posted data of a quick creation command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function rules(): array
        {
            return ['name' => 'required'];
        }

        public function update($id, array $data)
        {
            return 1;
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
            ['data' => ['name' => '']],
        )
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});

it('returns a link action on a quick creation command with a form with configureDisplayShowPageAfterCreation', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureDisplayShowPageAfterCreation();
        }

        public function update($id, array $data)
        {
            return 4;
        }
    });

    $this->get(route('code16.sharp.list', ['person']));

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
            ['data' => ['name' => 'Marie Curie']],
        )
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => url('/sharp/s-list/person/s-show/person/4'),
        ]);
});

it('returns a link action on a quick creation in an EEL case command with a form with configureDisplayShowPageAfterCreation', function () {
    sharp()->config()->addEntity('colleague', PersonEntity::class);

    fakeListFor('colleague', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('colleague', new class() extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureDisplayShowPageAfterCreation();
        }

        public function update($id, array $data)
        {
            return 4;
        }
    });

    // Simulate a get of the person show page
    $this
        ->get(
            route('code16.sharp.show.show', [
                'parentUri' => 's-list/person/',
                'person',
                1,
            ])
        )
        ->assertOk();

    // Simulate a post of the colleague quick creation command from an EEL
    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['colleague']),
            ['data' => ['name' => 'Marie Curie']],
        )
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => url('/sharp/s-list/person/s-show/person/1/s-show/colleague/4'),
        ]);
});
