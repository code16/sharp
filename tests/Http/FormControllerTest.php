<?php

use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonForm;
use Code16\Sharp\Tests\Fixtures\Entities\PersonSingleForm;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();
//    $this->withoutExceptionHandling();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('gets form data for an instance', function () {
    fakeFormFor('person', new class extends PersonForm {
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
    fakeFormFor('person', new class extends PersonForm {
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
    fakeFormFor('person', new class extends PersonForm {
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
    fakeFormFor('person', new class extends PersonForm {
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
    fakeFormFor('person', new class extends PersonForm {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('job'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout
                ->addColumn(6, function (FormLayoutColumn $column) {
                    return $column->withSingleField('name');
                })
                ->addColumn(6, function (FormLayoutColumn $column) {
                    return $column->withSingleField('job');
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

it('returns configuration', function () {
    fakeFormFor('person', new class extends PersonForm {
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
    fakeFormFor('person', new class extends PersonForm {
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

it('validates an instance before update', function () {
    fakeFormFor('person', new class extends PersonForm {
        public function validateRequest(): void
        {
            Validator::make(request()->all(), ['name' => 'required'])
                ->validate();
        }
    });

    $this
        ->post('/sharp/s-list/person/s-form/person', [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('handles application exception as 417', function () {
    fakeFormFor('person', new class extends PersonForm {
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
    config()->set(
        'sharp.entities.single-person',
        SinglePersonEntity::class,
    );
    
    fakeFormFor('single-person', new class extends PersonSingleForm {
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
    $this->withoutExceptionHandling();
    config()->set(
        'sharp.entities.single-person',
        SinglePersonEntity::class,
    );
    
    $this
        ->post('/sharp/s-show/single-person/s-form/single-person', [
            'name' => 'Stephen Hawking',
        ])
        ->assertRedirect('/sharp/s-show/single-person');
});