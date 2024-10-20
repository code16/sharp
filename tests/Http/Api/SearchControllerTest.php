<?php

use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Links\LinkToShowPage;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
    login();
});

it('returns result on a valid search', function () {
    $this->withoutExceptionHandling();

    sharp()->config()->enableGlobalSearch(
        new class extends SharpSearchEngine
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
                'results' => [
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
        new class extends SharpSearchEngine
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
                    'results' => [],
                    'emptyStateLabel' => 'Nobody found',
                ],
            ]
        );
});

it('allows to configure hide when empty', function () {
    sharp()->config()->enableGlobalSearch(
        new class extends SharpSearchEngine
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
                    'results' => [],
                    'showWhenEmpty' => false,
                ],
            ]
        );
});

it('raises validation errors', function () {
    sharp()->config()->enableGlobalSearch(
        new class extends SharpSearchEngine
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
                    'results' => [],
                    'validationErrors' => [
                        'Too short',
                        'Must start with a',
                    ],
                ],
            ]
        );
});

it('handles multiple result sets', function () {
    sharp()->config()->enableGlobalSearch(
        new class extends SharpSearchEngine
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
                    'results' => [
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
                    'results' => [
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
        new class extends SharpSearchEngine
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
