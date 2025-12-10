<?php

use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonSingleForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Support\Facades\Exceptions;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('gets form data for an instance', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
            ];
        }
    });

    $this->get('/sharp/root/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'James Clerk Maxwell')
            ->where('form.title', 'Edit “person”')
        );
});

it('gets form initial data for an entity in creation', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function create(): array
        {
            return [
                'name' => 'Who is this guy?',
            ];
        }
    });

    $this->get('/sharp/root/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Who is this guy?')
            ->where('form.title', 'New “person”')
        );
});

it('filters out data which is not a field', function () {
    fakeFormFor('person', new class() extends PersonForm
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

    $this->get('/sharp/root/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('form.data.name')
            ->missing('form.data.age')
        );
});

it('returns configured form fields', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormCheckField::make('is_nice', 'Is nice'));
        }
    });

    $this->get('/sharp/root/s-list/person/s-form/person/1')
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
    fakeFormFor('person', new class() extends PersonForm
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

    $this->get('/sharp/root/s-list/person/s-form/person/1')
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

it('stores or updates an instance and redirect to the list', function () {
    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirectContains('/sharp/root/s-list/person')
        ->assertRedirectContains('highlighted_entity_key=person')
        ->assertRedirectContains('highlighted_instance_id=1');

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirectContains('/sharp/root/s-list/person')
        ->assertRedirectContains('highlighted_entity_key=person')
        ->assertRedirectContains('highlighted_instance_id=1');
});

it('redirects to the show after an update', function () {
    $this->get('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');

    $this
        ->post('/sharp/s-list/person/s-show/person/1/s-form/person/1', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirectContains('/sharp/root/s-list/person/s-show/person/1')
        ->assertRedirectContains('highlighted_entity_key=person')
        ->assertRedirectContains('highlighted_instance_id=1');
});

it('creates an instance and redirect to the show if configured', function () {
    fakeFormFor('person', new class() extends PersonForm
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
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1');
});

it('logs an error if the update() method does not return the instance id', function () {
    Exceptions::fake();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function update(mixed $id, array $data) {}
    });

    $this
        ->post('/sharp/s-list/person/s-form/person')
        ->assertRedirect()
        ->assertSessionDoesntHaveErrors();

    Exceptions::assertReported(SharpFormUpdateException::class);
});

it('validates an instance before store and update with the rules() method', function () {
    fakeFormFor('person', new class() extends PersonForm
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

it('passes the formatted data to the rules() method', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }

        public function rules(array $data): array
        {
            return [
                'name' => [
                    'required',
                    function ($attribute, $value, $fail) use ($data) {
                        if ($data['job'] == 'Physicist' && $value == 'Marie Curie') {
                            $fail('Marie Curie is not a physicist');
                        }
                    },
                ],
            ];
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person/1', [
            'job' => 'Physicist',
            'name' => 'Marie Curie',
        ])
        ->assertSessionHasErrors('name');
});

it('validates an instance before store and update with a validate() call', function () {
    fakeFormFor('person', new class() extends PersonForm
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
    fakeFormFor('person', new class() extends PersonForm
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

it('calls prepareForValidation() before validation on applicable fields', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormAutocompleteListField::make('jobs')
                    ->setItemField(
                        SharpFormAutocompleteLocalField::make('job')
                            ->setLocalValues([
                                1 => 'Physicist',
                                2 => 'Chemist',
                            ])
                    )
            );
        }

        public function update($id, array $data)
        {
            $this->validate($data, ['jobs.*.job' => 'required']);
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'jobs' => [
                ['id' => null,  'job' => ['id' => 1, 'label' => 'Physicist']],
                ['id' => null],
            ],
        ])
        ->assertSessionDoesntHaveErrors('jobs.0.job')
        ->assertSessionHasErrors('jobs.1.job');
});

it('handles application exception as 417', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function find($id): array
        {
            throw new SharpApplicativeException('this person is a myth');
        }
    });

    $this
        ->get('/sharp/root/s-list/person/s-form/person/5')
        ->assertStatus(417);
});

it('gets form data for an instance in a single form case', function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    fakeFormFor('single-person', new class() extends PersonSingleForm
    {
        public function findSingle(): array
        {
            return [
                'name' => 'Ernest Rutherford',
            ];
        }
    });

    $this->get('/sharp/root/s-show/single-person/s-form/single-person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Ernest Rutherford')
        );
});

it('updates an instance on a single form case', function () {
    sharp()->config()->declareEntity(SinglePersonEntity::class);

    $this
        ->post('/sharp/s-show/single-person/s-form/single-person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/root/s-show/single-person');
});

it('gets form data for an instance of a sub entity (multiforms case)', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setMultiforms([
            'nobelized' => [
                new class() extends PersonForm
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
                new class() extends PersonForm
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

    $this->get('/sharp/root/s-list/person/s-form/person:nobelized/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Marie Curie')
        );

    $this->get('/sharp/root/s-list/person/s-form/person:nope/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.name', 'Rosalind Franklin')
        );
});

it('allows to configure a page alert', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert')
                ->setButton('My button', 'https://example.com');
        }
    });

    $this->get('/sharp/root/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.pageAlert', [
                'level' => PageAlertLevel::Info->value,
                'text' => 'My page alert',
                'buttonLabel' => 'My button',
                'buttonUrl' => 'https://example.com',
            ])
            ->etc()
        );
});

it('allows to configure a page alert with a closure as content', function () {
    fakeFormFor('person', new class() extends PersonForm
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

    $this->get('/sharp/root/s-list/person/s-form/person/1')
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
    fakeFormFor('person', new class() extends PersonForm
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

it('formats form title based on parent show breadcrumb', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }

        public function find($id): array
        {
            return $this->transform([
                'id' => 1,
                'name' => 'Marie Curie',
            ]);
        }
    });

    fakeFormFor('person', new PersonForm());

    $this->get('/sharp/root/s-list/person/s-show/person/1')
        ->assertOk();

    $this->get('/sharp/root/s-list/person/s-show/person/1/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.title', 'Edit “Marie Curie”')
        );
});

it('formats form title based on configured breadcrumb attribute', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }

        public function find($id): array
        {
            return $this->transform([
                'id' => 1,
                'name' => 'Marie Curie',
            ]);
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }

        public function find($id): array
        {
            return $this->transform([
                'id' => 1,
                'name' => 'Albert Einstein',
            ]);
        }
    });

    $this->get('/sharp/root/s-list/person/s-show/person/1')
        ->assertOk();

    $this->get('/sharp/root/s-list/person/s-show/person/1/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.title', 'Edit “Albert Einstein”')
        );
});

it('allows to override entirely the form title', function () {
    fakeFormFor('person', new class() extends PersonForm
    {
        public function buildFormConfig(): void
        {
            $this->configureEditTitle('My custom edit title')
                ->configureCreateTitle('My custom create title');
        }
    });

    $this->get('/sharp/root/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.title', 'My custom edit title')
        );

    $this->get('/sharp/root/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.title', 'My custom create title')
        );
});

it('handles html fields', function () {
    fakeFormFor('person', new class() extends SharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('name')
                )
                ->addField(
                    SharpFormHtmlField::make('html_string')
                        ->setTemplate('<h1>{{ $name }}</h1><p>{{ $text }}</p>')
                )
                ->addField(
                    SharpFormHtmlField::make('html_view')
                        ->setTemplate(view('fixtures::form-html-field'))
                )
                ->addField(
                    SharpFormHtmlField::make('html_closure')
                        ->setTemplate(fn ($data) => sprintf('<h1>%s</h1><p>%s</p>', $data['name'], $data['text'])
                        )
                )
                ->addField(
                    SharpFormListField::make('list')
                        ->addItemField(
                            SharpFormTextField::make('list_name')
                        )
                        ->addItemField(
                            SharpFormHtmlField::make('list_html')
                                ->setTemplate(fn ($data) => sprintf('<h1>%s</h1><p>%s</p>', $data['list_name'], $data['text'])
                                )
                        )
                );
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function (FormLayoutColumn $column) {
                $column->withField('html_string')
                    ->withField('html_view')
                    ->withField('html_closure')
                    ->withListField('list', function (FormLayoutColumn $column) {
                        $column->withField('list_name')
                            ->withField('list_html');
                    });
            });
        }

        public function find($id): array
        {
            return $this->setCustomTransformer('html_string', fn ($value) => ['text' => 'example'])
                ->setCustomTransformer('html_view', fn ($value) => ['text' => 'example'])
                ->setCustomTransformer('html_closure', fn ($value) => ['text' => 'example'])
                ->transform([
                    'name' => 'Albert Einstein',
                    'list' => [
                        ['id' => 1, 'list_name' => 'Marie Curie', 'list_html' => ['text' => 'example']],
                    ],
                ]);
        }

        public function update(mixed $id, array $data) {}
    });

    $this->get('/sharp/root/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.data.html_string', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.html_view', "<h1>Albert Einstein</h1><p>example</p>\n")
            ->where('form.data.html_closure', '<h1>Albert Einstein</h1><p>example</p>')
            ->where('form.data.list.0.list_html', '<h1>Marie Curie</h1><p>example</p>')
        );
});
