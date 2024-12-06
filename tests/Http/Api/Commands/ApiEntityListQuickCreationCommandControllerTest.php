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
