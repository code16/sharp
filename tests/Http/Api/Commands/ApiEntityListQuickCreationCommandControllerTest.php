<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\EntityList\EntityListEntities;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Tests\Fixtures\Entities\PersonChemistEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonPhysicistEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Facades\Exceptions;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
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
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
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
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
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
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
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
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
            ['data' => ['name' => 'Marie Curie', 'job' => 'Scientist']],
        )
        ->assertOk()
        ->assertJson(['action' => 'reload']);
});

it('logs an error if the form’s update() method does not return the instance id', function () {
    Exceptions::fake();

    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function update($id, array $data) {}
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
            ['data' => []],
        )
        ->assertOk()
        ->assertJson(['action' => 'reload']);

    Exceptions::assertReported(SharpFormUpdateException::class);
});

it('sharp()->context()->breadcrumb() is correct', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor('person', new class() extends PersonForm
    {
        public function update($id, array $data)
        {
            expect(sharp()->context())
                ->isCreation()->toBeTrue()
                ->isForm()->toBeTrue()
                ->entityKey()->toBe('person');

            expect(sharp()->context()->breadcrumb()->getCurrentSegmentUrl())
                ->toBe(url('/sharp/root/s-list/person/s-form/person'));
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
            ['data' => []],
            [
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person'),
            ]
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
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
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

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
            ['data' => ['name' => 'Marie Curie']],
            headers: [
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person'),
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => url('/sharp/root/s-list/person/s-show/person/4'),
        ]);
});

it('returns a link action on a quick creation in an EEL case command with a form with configureDisplayShowPageAfterCreation', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    fakeListFor(PersonEntity::class, new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    fakeFormFor(PersonEntity::class, new class() extends PersonForm
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

    // Simulate a post of the colleague quick creation command from an EEL
    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
            ['data' => ['name' => 'Marie Curie']],
            headers: [
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
            ]
        )
        ->assertOk()
        ->assertJson([
            'action' => 'link',
            'link' => url('/sharp/root/s-list/person/s-show/person/1/s-show/person/4'),
        ]);
});

it('checks for entity authorizations when using a quick creation command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreationForm();
        }
    });

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['create']);

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
        )
        ->assertForbidden();

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person',
            ]),
        )
        ->assertForbidden();
});

it('allows to call and post a quick creation command with the form of a sub-entity of the list', function () {
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this
                ->configureQuickCreationForm()
                ->configureEntityMap(
                    attribute: 'job',
                    entities: EntityListEntities::make()
                        ->addEntity('physicist', PersonPhysicistEntity::class)
                );
        }
    });

    fakeFormFor('person-physicist', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('physicist_name'));
        }

        public function update($id, array $data)
        {
            expect($data)->toBe(['physicist_name' => 'Rosalind Franklin']);

            return 1;
        }
    });

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-physicist',
            ]),
        )
        ->assertOk()
        ->assertJson(['fields' => ['physicist_name' => ['key' => 'physicist_name']]]);

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.store', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-physicist',
            ]),
            ['data' => ['physicist_name' => 'Rosalind Franklin']],
        )
        ->assertOk()
        ->assertJson(['action' => 'reload']);
});

it('does not allow to use the form of an entity which is not the list entity or one of its sub-entities', function () {
    sharp()->config()->declareEntity(PersonChemistEntity::class);
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this
                ->configureQuickCreationForm()
                ->configureEntityMap(
                    attribute: 'job',
                    entities: EntityListEntities::make()
                        ->addEntity('physicist', PersonPhysicistEntity::class)
                );
        }
    });

    fakeFormFor('person-chemist', new class() extends PersonForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('chemist_name'));
        }

        public function update($id, array $data)
        {
            throw new Exception('The form of an unrelated entity should not be updated');
        }
    });

    // The chemist entity itself is fully allowed: the form must be refused because
    // it is neither the list entity nor one of its sub-entities.
    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-chemist',
            ]),
        )
        ->assertForbidden()
        ->assertJsonMissingPath('fields');

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.store', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-chemist',
            ]),
            ['data' => ['chemist_name' => 'Evil']],
        )
        ->assertForbidden();
});

it('checks for create authorization on the form entity when using a quick creation command', function () {
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);

    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this
                ->configureQuickCreationForm()
                ->configureEntityMap(
                    attribute: 'job',
                    entities: EntityListEntities::make()
                        ->addEntity('physicist', PersonPhysicistEntity::class)
                );
        }
    });

    fakeFormFor('person-physicist', new class() extends PersonForm
    {
        public function update($id, array $data)
        {
            throw new Exception('The form of a forbidden entity should not be updated');
        }
    });

    fakePolicyFor('person-physicist', new class() extends SharpEntityPolicy
    {
        public function create($user): bool
        {
            return false;
        }
    });

    $this
        ->getJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-physicist',
            ]),
        )
        ->assertForbidden();

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.store', [
                'entityKey' => 'person',
                'formEntityKey' => 'person-physicist',
            ]),
            ['data' => []],
        )
        ->assertForbidden();
});
