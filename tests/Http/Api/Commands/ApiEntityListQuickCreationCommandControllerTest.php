<?php

use Code16\Sharp\Form\Fields\SharpFormTextField;
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
            $this->configureQuickCreation();
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
            route('code16.sharp.api.list.command.quickCreate.create', ['person']),
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
            $this->configureQuickCreation(['name']);
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
            route('code16.sharp.api.list.command.quickCreate.create', ['person']),
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
            route('code16.sharp.api.list.command.quickCreate.create', ['person']),
        )
        ->assertStatus(403);
});

it('allows to post a quick creation command', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function buildListConfig(): void
        {
            $this->configureQuickCreation();
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
            route('code16.sharp.api.list.command.quickCreate.create', ['person']),
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
            $this->configureQuickCreation();
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
            route('code16.sharp.api.list.command.quickCreate.create', ['person']),
            ['data' => ['name' => '']],
        )
        ->assertStatus(422)
        ->assertJsonValidationErrors('name');
});
