<?php

use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('gets form data for an instance', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'James Clerk Maxwell')
        );
});

it('gets form initial data for an entity in creation', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function create(): array
        {
            return [
                'name' => 'Who is this guy?',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Who is this guy?')
        );
});

it('filters out data which is not a field', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
                'age' => 22,
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('form.data.name')
            ->missing('form.data.age')
        );
});

it('returns configured form fields', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormCheckField::make('is_nice', 'Is nice'));
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('form.fields.name', fn (Assert $page) => $page
                ->where('type', 'text')
                ->etc()
            )
            ->has('form.fields.is_nice', fn (Assert $page) => $page
                ->where('type', 'check')
                ->etc()
            )
        );
});

it('returns configured form layout', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout
                ->addColumn(6, function (FormLayoutColumn $column) {
                    return $column->withField('name');
                })
                ->addColumn(6, function (FormLayoutColumn $column) {
                    return $column->withField('job');
                });
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('form.layout.tabs.0.columns.0', fn (Assert $page) => $page
                ->where('size', 6)
                ->where('fields.0.0.key', 'name')
                ->etc()
            )
            ->has('form.layout.tabs.0.columns.1', fn (Assert $page) => $page
                ->where('size', 6)
                ->where('fields.0.0.key', 'job')
                ->etc()
            )
        );
});

it('returns form configuration', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.config.breadcrumbAttribute', 'name')
        );
});

it('stores or updates an instance and redirect to the list', function () {
    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-list/person');

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-list/person');
});

it('redirects to the show after an update', function () {
    $this->get('/sharp/s-list/person/s-show/person/1/s-form/person/1');

    $this
        ->post('/sharp/s-list/person/s-show/person/1/s-form/person/1', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-list/person/s-show/person/1');
});

it('creates an instance and redirect to the show if configured', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureDisplayShowPageAfterCreation();
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-list/person/s-show/person/1');
});

it('validates an instance before store and update with the rules() method', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function rules(): array
        {
            return ['name' => 'required'];
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('validates an instance before store and update with a validate() call', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function update($id, array $data)
        {
            $this->validate($data, ['name' => 'required']);
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('formats data before validation', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormEditorField::make('name'));
        }

        public function update($id, array $data)
        {
            $this->validate($data, ['name' => 'nullable|string|min:3']);
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => ['text' => 'ba'],
        ])
        ->assertSessionHasErrors('name');
});

it('handles application exception as 417', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function find($id): array
        {
            throw new SharpApplicativeException('this person is a myth');
        }
    });

    $this
        ->get('/sharp/s-list/person/s-form/person/5')
        ->assertStatus(417);
});

it('gets form data for an instance in a single form case', function () {
    sharp()->config()->addEntity('single-person', SinglePersonEntity::class);

    fakeFormFor('single-person', new class extends PersonSingleForm
    {
        public function findSingle(): array
        {
            return [
                'name' => 'Ernest Rutherford',
            ];
        }
    });

    $this->get('/sharp/s-show/single-person/s-form/single-person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Ernest Rutherford')
        );
});

it('updates an instance on a single form case', function () {
    sharp()->config()->addEntity('single-person', SinglePersonEntity::class);

    $this
        ->post('/sharp/s-show/single-person/s-form/single-person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-show/single-person');
});

it('gets form data for an instance of a sub entity (multiforms case)', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setMultiforms([
            'nobelized' => [
                new class extends PersonForm
                {
                    public function find($id): array
                    {
                        return [
                            'id' => 1,
                            'name' => 'Marie Curie',
                            'nobel' => 'nobelized',
                        ];
                    }
                },
                'With Nobel prize',
            ],
            'nope' => [
                new class extends PersonForm
                {
                    public function find($id): array
                    {
                        return [
                            'id' => 2,
                            'name' => 'Rosalind Franklin',
                            'nobel' => 'nope',
                        ];
                    }
                },
                'No Nobel prize',
            ],
        ]);

    $this->get('/sharp/s-list/person/s-form/person:nobelized/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Marie Curie')
        );

    $this->get('/sharp/s-list/person/s-form/person:nope/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Rosalind Franklin')
        );
});

it('allows to configure a page alert', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class extends PersonForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert');
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.pageAlert', [
                'level' => \Code16\Sharp\Enums\PageAlertLevel::Info->value,
                'text' => 'My page alert',
            ])
            ->etc()
        );
});

it('allows to configure a page alert with a closure as content', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage(function (array $data) {
                    return 'Hello '.$data['name'];
                });
        }

        public function find($id): array
        {
            return [
                'id' => 1,
                'name' => 'Marie Curie',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'Hello Marie Curie',
            ])
            ->etc()
        );
});

it('allows to use the legacy validation', function () {
    fakeFormFor('person', new class extends PersonForm
    {
        protected string $formValidatorClass = \Code16\Sharp\Tests\Fixtures\Sharp\PersonLegacyValidator::class;
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});
