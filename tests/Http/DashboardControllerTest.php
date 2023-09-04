<?php

use Code16\Sharp\EntityList\Commands\EntityState;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonShow;
use Code16\Sharp\Tests\Fixtures\Entities\PersonSingleShow;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

it('gets dashboard widgets', function () {
    fakeShowFor('person', new class extends PersonShow {
        public function find($id): array
        {
            return [
                'name' => 'James Clerk Maxwell',
            ];
        }
    });

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('show.data.name', 'James Clerk Maxwell')
        );
});

///** @test */
//public function we_can_get_dashboard_layout()
//{
//    $this->buildTheWorld();
//
//    $this->getJson(route('code16.sharp.api.dashboard', 'personal_dashboard'))
//        ->assertOk()
//        ->assertJson([
//            'layout' => [
//                'sections' => [
//                    [
//                        'key' => null,
//                        'title' => '',
//                        'rows' => [
//                            [
//                                ['key' => 'bars', 'size' => 12],
//                            ], [
//                                ['key' => 'panel', 'size' => 4],
//                                ['key' => 'bars2', 'size' => 8],
//                            ],
//                        ],
//                    ],
//                ],
//            ],
//        ]);
//}
//
///** @test */
//public function we_can_get_dashboard_data()
//{
//    $this->buildTheWorld();
//
//    $this->getJson(route('code16.sharp.api.dashboard', 'personal_dashboard'))
//        ->assertOk()
//        ->assertJson([
//            'data' => [
//                'bars1' => [
//                    'key' => 'bars1',
//                    'datasets' => [
//                        [
//                            'data' => [10, 20, 30],
//                            'label' => 'Bars 1',
//                        ],
//                    ],
//                    'labels' => ['a', 'b', 'c'],
//                ],
//                'bars2' => [
//                    'key' => 'bars2',
//                    'datasets' => [
//                        [
//                            'data' => [10, 20, 30],
//                            'label' => 'Bars 2',
//                        ],
//                    ],
//                    'labels' => ['a', 'b', 'c'],
//                ],
//                'panel' => [
//                    'key' => 'panel',
//                    'data' => [
//                        'name' => 'John Wayne',
//                    ],
//                ],
//            ],
//        ]);
//}
