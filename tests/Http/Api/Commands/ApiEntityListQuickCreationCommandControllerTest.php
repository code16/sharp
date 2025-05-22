<?php

use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonForm;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
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
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
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

            expect(sharp()->context()->breadcrumb())
                ->getCurrentSegmentUrl()->toBe(url('/sharp/s-list/person/s-form/person'));
        }
    });

    $this
        ->postJson(
            route('code16.sharp.api.list.command.quick-creation-form.create', ['person']),
            ['data' => []],
            [
                SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/s-list/person'),
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
