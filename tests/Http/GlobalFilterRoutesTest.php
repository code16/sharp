<?php

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Utils\Fields\FieldsContainer;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    URL::defaults(['globalFilter' => null]);
    login();
});

it('redirects to route with default globalFilter when missing', function () {
    sharp()->config()
        ->declareEntity(DashboardEntity::class)
        ->declareEntity(SinglePersonEntity::class);

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/root/s-list/person');

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1');

    $this->get('/sharp/s-list/person/s-show/person/1/s-form/person/1')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');

    $this->get('/sharp/s-list/person/s-show/person/1/s-show/person/2/s-form/person/2')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1/s-show/person/2/s-form/person/2');

    $this->get('/sharp/s-dashboard/dashboard')
        ->assertRedirect('/sharp/root/s-dashboard/dashboard');

    $this->get('/sharp/s-show/single-person')
        ->assertRedirect('/sharp/root/s-show/single-person');
});

it('redirects to route with correct globalFilter when missing and global filters are defined', function () {
    fakeGlobalFilter();

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/two/s-list/person');
});

it('sets the current globalFilter according to the URL', function () {
    fakeGlobalFilter();

    $this->get('/sharp/one/s-list/person/s-show/person/1')
        ->assertOk();

    expect(sharp()->context()->globalFilterValue('test'))->toEqual('one');
});

it('sets the current globalFilter according to the URL for API routes', function () {
    fakeGlobalFilter();
    fakeListFor('person', new class() extends PersonList
    {
        protected function getEntityCommands(): ?array
        {
            return [
                'cmd' => new class() extends EntityCommand
                {
                    public function label(): ?string
                    {
                        return 'entity';
                    }

                    public function buildFormFields(FieldsContainer $formFields): void
                    {
                        $formFields->addField(SharpFormTextField::make('name'));
                    }

                    public function execute(array $data = []): array {}
                },
            ];
        }
    });

    $this
        ->getJson('/sharp/api/one/list/person/command/cmd/form')
        ->assertOk();

    expect(sharp()->context()->globalFilterValue('test'))->toEqual('one');
});

it('redirects to the corresponding route with valid globalFilter if an invalid globalFilter is set in the URL', function () {
    fakeGlobalFilter();

    $this->get('/sharp/five/s-list/person/s-show/person/1?highlight=1')
        ->assertRedirect('/sharp/two/s-list/person/s-show/person/1?highlight=1');

    expect(sharp()->context()->globalFilterValue('test'))->toEqual('two');
});

it('redirects to the corresponding route with valid globalFilter if a different number of filters is sent', function () {
    fakeGlobalFilter('test1');
    fakeGlobalFilter('test2');

    $this->get('/sharp/two/s-list/person/s-show/person/1?highlight=1')
        ->assertRedirect('/sharp/two~two/s-list/person/s-show/person/1?highlight=1');
});

it('redirects to route with correct globalFilters when missing and multiple global filters are defined', function () {
    fakeGlobalFilter('test1');
    fakeGlobalFilter('test2');

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/two~two/s-list/person');
});

it('sets the current multiple globalFilters according to the URL', function () {
    fakeGlobalFilter('test1');
    fakeGlobalFilter('test2');

    $this->get('/sharp/one~two/s-list/person/s-show/person/1')
        ->assertOk();

    expect(sharp()->context()->globalFilterValue('test1'))->toEqual('one');
    expect(sharp()->context()->globalFilterValue('test2'))->toEqual('two');
});
