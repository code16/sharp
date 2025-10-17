<?php

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Show\Fields\SharpShowDashboardField;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonPhysicistEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Tests\Unit\EntityList\Fakes\FakeSharpEntityList;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();
    sharp()->config()->declareEntity(PersonEntity::class);
});

it('allows to configure a policy', function () {
    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function update($user, $instanceId): bool
        {
            return $instanceId == 1;
        }

        public function delete($user, $instanceId): bool
        {
            return false;
        }
    });

    $this->get('/sharp/s-list/person')->assertOk();
    $this->get('/sharp/s-list/person/s-show/person/1')->assertOk();
    $this->get('/sharp/s-list/person/s-form/person/1')->assertOk();
    $this->get('/sharp/s-list/person/s-form/person')->assertOk();

    $this->post('/sharp/s-list/person/s-form/person/1')->assertRedirect();
    $this->post('/sharp/s-list/person/s-form/person')->assertRedirect();

    $this->delete('/sharp/s-list/person/s-show/person/50')->assertForbidden();

    // Update policy with an id > 1 returns 403
    $this->post('/sharp/s-list/person/s-form/person/2')->assertForbidden();
});

it('returns policies with a show or form get request', function () {
    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function update($user, $instanceId): bool
        {
            return $instanceId == 1;
        }

        public function delete($user, $instanceId): bool
        {
            return false;
        }
    });

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
                'update' => true,
                'create' => true,
                'view' => true,
            ])
        );

    $this->get('/sharp/s-list/person/s-show/person/2')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.authorizations', [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => true,
            ])
        );
});

it('allows to access to the form in readonly mode if there is no show', function () {
    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function update($user, $instanceId): bool
        {
            return $instanceId == 1;
        }
    });

    $this->get('/sharp/s-list/person/s-form/person/2')->assertForbidden();

    fakeShowFor('person', null);

    $this->get('/sharp/s-list/person/s-form/person/2')->assertOk();
});

it('returns policies with a list get request', function () {
    fakeListFor('person', new class() extends FakeSharpEntityList
    {
        public function getListData(): array|Arrayable
        {
            return [
                ['id' => 1, 'name' => 'Marie Curie'],
                ['id' => 2, 'name' => 'Niels Bohr'],
            ];
        }
    });

    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function delete($user, $instanceId): bool
        {
            return $instanceId == 1;
        }
    });

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('entityList.authorizations', [
                'reorder' => true,
                'create' => true,
            ])
            ->where('entityList.data.0._meta.authorizations', [
                'view' => true,
                'delete' => true,
            ])
            ->where('entityList.data.1._meta.authorizations', [
                'view' => true,
                'delete' => false,
            ])
        );
});

it('returns policies with a SharpShowEntityListField on a show get request', function () {
    sharp()->config()->declareEntity(PersonPhysicistEntity::class);

    fakeShowFor(PersonEntity::$entityKey, new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowEntityListField::make(PersonPhysicistEntity::class));
        }
    });

    fakePolicyFor(PersonPhysicistEntity::$entityKey, new class() extends SharpEntityPolicy
    {
        public function entity($user): bool
        {
            return $user->name == 'admin';
        }
    });

    $this
        ->actingAs(new User(['name' => 'bob']))
        ->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.fields.'.PersonPhysicistEntity::class.'.authorizations', [
                'view' => false,
            ])
        );

    $this
        ->actingAs(new User(['name' => 'admin']))
        ->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.fields.'.PersonPhysicistEntity::class.'.authorizations', [
                'view' => true,
            ])
        );
});

it('returns policies with a SharpShowDashboardField on a show get request', function () {
    sharp()->config()->declareEntity(DashboardEntity::class);

    fakeShowFor(PersonEntity::$entityKey, new class() extends PersonShow
    {
        public function buildShowFields(FieldsContainer $showFields): void
        {
            $showFields->addField(SharpShowDashboardField::make(DashboardEntity::class));
        }
    });

    fakePolicyFor(DashboardEntity::$entityKey, new class() extends SharpEntityPolicy
    {
        public function entity($user): bool
        {
            return $user->name == 'admin';
        }
    });

    $this
        ->actingAs(new User(['name' => 'bob']))
        ->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.fields.'.DashboardEntity::class.'.authorizations', [
                'view' => false,
            ])
        );

    $this
        ->actingAs(new User(['name' => 'admin']))
        ->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.fields.'.DashboardEntity::class.'.authorizations', [
                'view' => true,
            ])
        );
});

it('overrides policies with global authorizations', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setProhibitedActions(['update']);

    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function update($user, $instanceId): bool
        {
            return true;
        }
    });

    $this
        ->get('/sharp/s-list/person/s-show/person/1')
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.authorizations.update', false)
        );
});

it('allows to set the entity authorization in a policy', function () {
    fakePolicyFor('person', new class() extends SharpEntityPolicy
    {
        public function entity($user): bool
        {
            return $user->name != 'unauthorized-user';
        }
    });

    login(new User(['name' => 'unauthorized-user']));

    $this->get('/sharp/s-list/person')
        ->assertForbidden();
});

it('allows to set dashboard view policy to handle whole dashboard visibility', function () {
    sharp()->config()->declareEntity(DashboardEntity::class);

    fakePolicyFor(DashboardEntity::$entityKey, new class() extends SharpEntityPolicy
    {
        public function entity($user): bool
        {
            return $user->name != 'unauthorized-user';
        }
    });

    login(new User(['name' => 'unauthorized-user']));

    $this->get('/sharp/s-dashboard/'.DashboardEntity::$entityKey)->assertForbidden();
});

it('does not check view, update and delete policies on create case', function () {
    $this->get('/sharp/s-list/person/s-form/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('form.authorizations', [
                'delete' => false,
                'update' => false,
                'create' => true,
                'view' => false,
            ])
        );
});
