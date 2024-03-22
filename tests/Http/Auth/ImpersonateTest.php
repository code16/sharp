<?php

use Code16\Sharp\Auth\Impersonate\SharpDefaultEloquentImpersonationHandler;
use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;

function migrateUsersTable()
{
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->timestamps();
    });
}

it('redirects to impersonation page if enabled and guest', function () {
    config()->set('sharp.auth.impersonate.handler', new class() extends SharpImpersonationHandler
    {
        public function enabled(): bool
        {
            return true;
        }

        public function getUsers(): array
        {
            return [];
        }
    });

    $this->get(route('code16.sharp.home'))
        ->assertRedirect(route('code16.sharp.impersonate'));
});

it('displays impersonatable users from a custom handler', function () {
    $users = [
        1 => 'Marie Curie',
        2 => 'Albert Einstein',
    ];

    config()->set('sharp.auth.impersonate.handler', new class($users) extends SharpImpersonationHandler
    {
        public function __construct(private readonly array $users)
        {
        }

        public function enabled(): bool
        {
            return true;
        }

        public function getUsers(): array
        {
            return $this->users;
        }
    });

    $this->get(route('code16.sharp.impersonate'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('impersonateUsers', $users)
        );
});

it('allow to use default eloquent implementation handler', function () {
    migrateUsersTable();

    config()->set('sharp.auth.login_attribute', 'name');
    // We override the default handler just to skip the enabled() check
    config()->set('sharp.auth.impersonate.handler', new class() extends SharpDefaultEloquentImpersonationHandler
    {
        public function enabled(): bool
        {
            return true;
        }
    });

    User::create(['id' => 10, 'name' => 'Marie Curie']);
    User::create(['id' => 20, 'name' => 'Albert Einstein']);

    $this->get(route('code16.sharp.impersonate'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('impersonateUsers', [
                10 => 'Marie Curie',
                20 => 'Albert Einstein',
            ])
        );
});

it('does not display impersonatable users if impersonation is not enabled', function () {
    $users = [
        1 => 'Marie Curie',
        2 => 'Albert Einstein',
    ];

    config()->set('sharp.auth.impersonate.handler', new class($users) extends SharpImpersonationHandler
    {
        public function __construct(private readonly array $users)
        {
        }

        public function getUsers(): array
        {
            return $this->users;
        }
    });

    config()->set('sharp.auth.impersonate.enabled', false);
    $this->get(route('code16.sharp.impersonate'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('impersonateUsers', null)
        );

    // Even if the handler is enabled, since the env is not "local", it should not display the impersonation
    config()->set('sharp.auth.impersonate.enabled', true);
    $this->get(route('code16.sharp.impersonate'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('impersonateUsers', null)
        );
});

it('allows to impersonate a registered user', function () {
    migrateUsersTable();
    User::create(['id' => 1, 'name' => 'Marie Curie']);

    config()->set('sharp.auth.impersonate.handler', new class() extends SharpDefaultEloquentImpersonationHandler
    {
        public function enabled(): bool
        {
            return true;
        }
    });

    $this->withoutExceptionHandling();

    $this->post(route('code16.sharp.impersonate.post'), ['user_id' => 1])
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth()->user())
        ->id->toEqual(1)
        ->name->toEqual('Marie Curie');
});

it('does not allow to impersonate an existing user who is not listed in the handler', function () {
    migrateUsersTable();
    User::create(['id' => 1, 'name' => 'Marie Curie']);
    User::create(['id' => 2, 'name' => 'Albert Einstein']);

    config()->set('sharp.auth.impersonate.handler', new class() extends SharpDefaultEloquentImpersonationHandler
    {
        public function enabled(): bool
        {
            return true;
        }

        public function getUsers(): array
        {
            return [
                2 => 'Albert Einstein',
            ];
        }
    });

    $this->get(route('code16.sharp.login'));
    $this->post(route('code16.sharp.impersonate.post'), ['user_id' => 1])
        ->assertRedirect(route('code16.sharp.login'))
        ->assertSessionHasErrors('user_id');

    $this->post(route('code16.sharp.impersonate.post'), ['user_id' => 2])
        ->assertRedirect(route('code16.sharp.home'))
        ->assertSessionHasNoErrors();
});
