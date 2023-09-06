<?php

use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Links\LinkToShowPage;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('returns result on a valid search', function () {
    $this->withoutExceptionHandling();

    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $resultSet = $this->addResultSet('People');
                $resultSet->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
                $resultSet->addResultLink(LinkToShowPage::make('person', 2), 'Jane Ford', 'Some detail');
            }
        });

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
    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People')->setEmptyStateLabel('Nobody found');
            }
        });

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
    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People')->hideWhenEmpty();
            }
        });

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
    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
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
        });

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
    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $this->addResultSet('People', 'fa-user')
                    ->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');

                $this->addResultSet('Cars', 'fa-car')
                    ->addResultLink(LinkToShowPage::make('car', 1), 'Aston Martin');
            }
        });

    $this->getJson('/sharp/api/search?q=some-search')
        ->assertJson(
            [
                [
                    'label' => 'People',
                    'icon' => 'fa-user',
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
                    'icon' => 'fa-car',
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
    config()->set(
        'sharp.search.engine',
        fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                if ($terms[0] == '%john%' && $terms[1] == '%wayne%') {
                    $this->addResultSet('People', 'fa-user')
                        ->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
                }
            }
        });

    $this->getJson('/sharp/api/search?q=john+%20wayne%20')
        ->assertJsonFragment([
            'label' => 'John Wayne',
        ]);
});
