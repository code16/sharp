<?php

use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Code16\Sharp\Utils\Links\LinkToShowPage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('returns result on a valid search', function () {
    $this->withoutExceptionHandling();

    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $resultSet = $this->addResultSet('People');
                $resultSet->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
                $resultSet->addResultLink(LinkToShowPage::make('person', 2), 'Jane Ford', 'Some detail');
            }
        }
    );

    $this
        ->getJson('/sharp/api/search?q=some-search')
        ->assertJson([
            [
                'label' => 'People',
                'icon' => null,
                'resultLinks' => [
                    [
                        'label' => 'John Wayne',
                        'link' => url('sharp/s-list/person/s-show/person/1'),
                        'detail' => null,
                    ],
                    [
                        'label' => 'Jane Ford',
                        'link' => url('sharp/s-list/person/s-show/person/2'),
                        'detail' => 'Some detail',
                    ],
                ],
            ],
        ]);
});

it('allows to configure a custom empty state label', function () {
    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People')->setEmptyStateLabel('Nobody found');
            }
        }
    );

    $this->getJson('/sharp/api/search?q=some-search')
        ->assertJson(
            [
                [
                    'label' => 'People',
                    'icon' => null,
                    'resultLinks' => [],
                    'emptyStateLabel' => 'Nobody found',
                ],
            ]
        );
});

it('allows to configure hide when empty', function () {
    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People')->hideWhenEmpty();
            }
        }
    );

    $this->getJson('/sharp/api/search?q=some-search')
        ->assertJson(
            [
                [
                    'label' => 'People',
                    'icon' => null,
                    'resultLinks' => [],
                    'hideWhenEmpty' => true,
                ],
            ]
        );
});

it('raises validation errors', function () {
    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $resultSet = $this->addResultSet('People');
                $resultSet->validateSearch(
                    ['string', 'min:3', 'starts_with:a'],
                    ['min' => 'Too short', 'starts_with' => 'Must start with a']
                );
                $resultSet->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
            }
        }
    );

    $this->getJson('/sharp/api/search?q=bb')
        ->assertJson(
            [
                [
                    'label' => 'People',
                    'icon' => null,
                    'resultLinks' => [],
                    'validationErrors' => [
                        'Too short',
                        'Must start with a',
                    ],
                ],
            ]
        );
});

it('handles multiple result sets', function () {
    $this->withoutExceptionHandling();

    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People', 'testicon-user')
                    ->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');

                $this->addResultSet('Cars', 'testicon-car')
                    ->addResultLink(LinkToShowPage::make('car', 1), 'Aston Martin');
            }
        }
    );

    $this->getJson('/sharp/api/search?q=some-search')
        ->assertJson(
            [
                [
                    'label' => 'People',
                    'icon' => [
                        'name' => 'testicon-user',
                        'svg' => '<svg><!--user--></svg>',
                    ],
                    'resultLinks' => [
                        [
                            'label' => 'John Wayne',
                            'link' => url('sharp/s-list/person/s-show/person/1'),
                            'detail' => null,
                        ],
                    ],
                ],
                [
                    'label' => 'Cars',
                    'icon' => [
                        'name' => 'testicon-car',
                        'svg' => '<svg><!--car--></svg>',
                    ],
                    'resultLinks' => [
                        [
                            'label' => 'Aston Martin',
                            'link' => url('sharp/s-list/car/s-show/car/1'),
                            'detail' => null,
                        ],
                    ],
                ],
            ]
        );
});

it('allows multiple search terms', function () {
    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                if ($terms[0] == '%john%' && $terms[1] == '%wayne%') {
                    $this->addResultSet('People', 'testicon-user')
                        ->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
                }
            }
        }
    );

    $this->getJson('/sharp/api/search?q=john+%20wayne%20')
        ->assertJsonFragment([
            'label' => 'John Wayne',
        ]);
});

it('returns a 404 if not enabled', function () {
    sharp()->config()->disableGlobalSearch();

    $this
        ->getJson('/sharp/api/search?q=some-search')
        ->assertNotFound();
});

it('returns a 403 if not authorized', function () {
    sharp()->config()->enableGlobalSearch(
        new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $resultSet = $this->addResultSet('People');
                $resultSet->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
            }
            public function authorize(): bool
            {
                return auth()->user()->email == 'authorized-user@test.fr';
            }
        }
    );

    $this
        ->getJson('/sharp/api/search?q=some-search')
        ->assertForbidden();

    $this
        ->actingAs(User::make(['email' => 'authorized-user@test.fr']))
        ->getJson('/sharp/api/search?q=some-search')
        ->assertOk();
});

it('the global search is sent with every inertia request, if enabled and authorized', function () {
    sharp()->config()->addEntity('person', PersonEntity::class);

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('globalSearch', null)
        );

    sharp()->config()->enableGlobalSearch(
        engine: new class() extends SharpSearchEngine
        {
            public function searchFor(array $terms): void {}
            public function authorize(): bool
            {
                return auth()->user()->email == 'authorized-user@test.fr';
            }
        },
        placeholder: 'Search for something'
    );

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('globalSearch', null)
        );

    $this
        ->actingAs(User::make(['email' => 'authorized-user@test.fr']))
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('globalSearch', [
                'config' => [
                    'placeholder' => 'Search for something',
                ],
            ])
        );
});
