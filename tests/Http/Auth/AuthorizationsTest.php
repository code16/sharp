<?php

use Code16\Sharp\Auth\SharpAuthenticationCheckHandler;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();
    sharp()->config()->addEntity('person', PersonEntity::class);
});

it('allows to configure prohibited actions on entities', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete', 'create', 'update', 'view']);

    // Can't access to the show
    $this->get('/sharp/s-list/person/s-show/person/1')->assertForbidden();

    // Can't access to the form
    $this->get('/sharp/s-list/person/s-form/person')->assertForbidden();
    $this->get('/sharp/s-list/person/s-form/person/1')->assertForbidden();
    $this->post('/sharp/s-list/person/s-form/person')->assertForbidden();
    $this->post('/sharp/s-list/person/s-form/person/1')->assertForbidden();

    // Can't delete
    $this->deleteJson(route('code16.sharp.api.list.delete', ['person', 1]))->assertForbidden();
    $this->delete('/sharp/s-list/person/s-show/person/1')->assertForbidden();

    // We can still view the list
    $this->get('/sharp/s-list/person')->assertOk();
});

it('allows to access to the form in readonly mode if there is no show', function () {
    fakeShowFor('person', null);

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['update']);

    $this->get('/sharp/s-list/person/s-form/person/1')->assertOk();
    $this->post('/sharp/s-list/person/s-form/person/1')->assertForbidden();
});

it('handles default prohibited actions on entity', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete', 'update']);

    $this->get('/sharp/s-list/person')->assertOk();
    $this->get('/sharp/s-list/person/s-show/person/50')->assertOk();
    $this->get('/sharp/s-list/person/s-form/person')->assertOk();
    $this->get('/sharp/s-list/person/s-form/person/50')->assertForbidden();
    $this->post('/sharp/s-list/person/s-form/person')->assertRedirect();
    $this->post('/sharp/s-list/person/s-form/person/50')->assertForbidden();
    $this->delete('/sharp/s-list/person/s-show/person/50')->assertForbidden();
});

it('returns prohibited actions with a show or form get request', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete', 'update']);

    $this
        ->get('/sharp/s-list/person/s-form/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.authorizations', [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => false,
            ])
        );

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.authorizations', [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => true,
            ])
        );
});

it('returns prohibited actions with a list get request', function () {
    fakeListFor('person', new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];
        }
    });

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['delete']);

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.authorizations', [
                'delete' => [],
                'reorder' => true,
                'create' => true,
                'view' => [1, 2],
            ])
        );
});

it('allow access by default', function () {
    fakeListFor('person', new class extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];
        }
    });

    // Create (no instanceId, only create is allowed)
    $this
        ->get('/sharp/s-list/person/s-form/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.authorizations', [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => false,
            ])
        );

    // Edit
    $this
        ->get('/sharp/s-list/person/s-form/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.authorizations', [
                'delete' => true,
                'update' => true,
                'create' => true,
                'view' => true,
            ])
        );

    // EL (inertia)
    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.authorizations', [
                'delete' => [1, 2],
                'reorder' => true,
                'create' => true,
                'view' => [1, 2],
            ])
        );

    // EEL (json)
    $this
        ->getJson('/sharp/api/list/person')
        ->assertJsonFragment([
            'authorizations' => [
                'delete' => [1, 2],
                'reorder' => true,
                'create' => true,
                'view' => [1, 2],
            ],
        ]);
});

it('checks the main entity prohibited actions in case of a sub entity', function () {

    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setMultiforms([
            'big' => [FakeSharpForm::class],
        ])
        ->setProhibitedActions(['delete']);

    $this->get('/sharp/s-list/person/s-form/person:big')->assertOk();
    $this->post('/sharp/s-list/person/s-form/person:big')->assertRedirect();
    $this->get('/sharp/s-list/person/s-form/person:big/50')->assertOk();
    $this->post('/sharp/s-list/person/s-form/person:big/50')->assertRedirect();
    $this->delete('/sharp/s-list/person/s-show/person:big/50')->assertForbidden();
    $this->get('/sharp/s-list/person')->assertOk();
});

it('handles custom auth check', function () {
    $this->app['config']->set(
        'sharp.auth.check_handler',
        fn () => new class implements SharpAuthenticationCheckHandler
        {
            public function check($user): bool
            {
                return $user->name == 'ok';
            }
        }
    );

    login(new User(['name' => 'ok']));
    $this->get('/sharp/s-list/person')
        ->assertOk();

    login(new User(['name' => 'ko']));
    $this->get('/sharp/s-list/person')
        ->assertRedirect(route('code16.sharp.login'));
});

it('checks useSharp Gate', function () {
    Gate::define('viewSharp', fn ($user) => $user->name === 'ok');

    login(new User(['name' => 'ok']));
    $this->get('/sharp/s-list/person')
        ->assertOk();

    login(new User(['name' => 'ko']));
    $this->get('/sharp/s-list/person')
        ->assertRedirect(route('code16.sharp.login'));
});