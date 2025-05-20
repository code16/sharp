<?php

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Tests\Unit\Show\Fakes\FakeSharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('adds the locales array if configured to the form', function () {
    $this->withoutExceptionHandling();

    fakeFormFor('person', new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('name')
                    ->setLocalized()
            );
        }

        public function find(mixed $id): array
        {
            return [];
        }

        public function getDataLocalizations(): array
        {
            return ['fr', 'en'];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.locales', ['fr', 'en'])
        );

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.locales', ['fr', 'en'])
        );
});

it('throws when a localized field is configured without form locales', function () {
    fakeFormFor('person', new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('name')
                    ->setLocalized()
            );
        }

        public function find(mixed $id): array
        {
            return [];
        }
    });

    $this->withoutExceptionHandling();

    $this->get('/sharp/s-list/person/s-form/person');
})
    ->throws(\Code16\Sharp\Exceptions\SharpInvalidConfigException::class, 'The "name" field is localized but no locales are defined');

it('does not add the locales array if configured but there is no localized field', function () {
    fakeFormFor('person', new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('name')
            );
        }

        public function find(mixed $id): array
        {
            return [];
        }

        public function getDataLocalizations(): array
        {
            return ['fr', 'en'];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.locales', [])
        );
});

it('adds the locales array if configured in a form list field', function () {
    fakeFormFor('person', new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('name')
                    ->addItemField(
                        SharpFormTextField::make('name')->setLocalized(),
                    ),
            );
        }

        public function find(mixed $id): array
        {
            return [];
        }

        public function getDataLocalizations(): array
        {
            return ['fr', 'en'];
        }
    });

    $this->get('/sharp/s-list/person/s-form/person')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.locales', ['fr', 'en'])
        );

    $this->get('/sharp/s-list/person/s-form/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.locales', ['fr', 'en'])
        );
});

it('adds the locales array if configured in the show', function () {
    $this->withoutExceptionHandling();
    fakeShowFor('person', new class() extends FakeSharpShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(
                SharpShowTextField::make('name')->setLocalized()
            );
        }

        public function find(mixed $id): array
        {
            return [];
        }

        public function getDataLocalizations(): array
        {
            return ['fr', 'en'];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.locales', ['fr', 'en'])
        );
});
