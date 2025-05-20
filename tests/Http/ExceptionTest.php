<?php

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('renders inertia Exception', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            throw new Exception('Test error');
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertStatus(500)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 500)
            ->where('message', 'Server error')
        );
});

it('renders inertia HttpException', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            abort(404, 'Not found Test error');
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertStatus(404)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 404)
            ->where('message', 'Not found Test error')
        );
});

it('renders inertia SharpException', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            throw new SharpAuthorizationException('Unauthorized Test error');
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertStatus(403)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 403)
            ->where('message', 'Unauthorized Test error')
        );

    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            throw new SharpApplicativeException('Applicative Test error');
        }
    });

    $this->get('/sharp/s-list/person')
        ->assertStatus(417)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Error')
            ->where('status', 417)
            ->where('message', 'Applicative Test error')
        );
});

it('renders debug 500 error', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            new UnknownClass();

            return [];
        }
    });

    config()->set('app.debug', true);

    $response = $this->get('/sharp/s-list/person')
        ->assertStatus(500);

    // response is not rendered in inertia
    expect(fn () => $response->assertInertia())->toThrow('Not a valid Inertia response.');
});

it('renders debug 500 API error', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getListData(): array
        {
            new UnknownClass();

            return [];
        }
    });

    config()->set('app.debug', true);

    $this->getJson('/sharp/api/list/person')
        ->assertStatus(500)
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});
