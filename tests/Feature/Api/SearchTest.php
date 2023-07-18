<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\Links\LinkToShowPage;

class SearchTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->login();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function we_can_search_and_get_results()
    {
        config()->set('sharp.search.engine', fn () => new class extends SharpSearchEngine
        {
            public function searchFor(array $terms): void
            {
                $resultSet = $this->addResultSet('People');
                $resultSet->addResultLink(LinkToShowPage::make('person', 1), 'John Wayne');
                $resultSet->addResultLink(LinkToShowPage::make('person', 2), 'Jane Ford', 'Some detail');
            }
        });

        $this->getJson('/sharp/api/search?q=some-search')
            ->assertJson(
                [
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
                ]
            );
    }

    /** @test */
    public function we_can_configure_a_custom_empty_state_label()
    {
        config()->set('sharp.search.engine', fn () => new class extends SharpSearchEngine
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
    }

    /** @test */
    public function we_can_get_multiple_result_sets()
    {
        config()->set('sharp.search.engine', fn () => new class extends SharpSearchEngine
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
    }

    /** @test */
    public function search_terms_are_handled()
    {
        config()->set('sharp.search.engine', fn () => new class extends SharpSearchEngine
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
    }
}
