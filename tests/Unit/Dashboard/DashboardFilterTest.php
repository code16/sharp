<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Filters\DashboardSelectFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;

class DashboardFilterTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_dashboard_filters_config()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function getFilters(): ?array
            {
                return [
                    new class extends DashboardSelectFilter
                    {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test')
                                ->configureLabel('test_label');
                        }

                        public function values(): array
                        {
                            return [1 => 'A', 2 => 'B'];
                        }
                    },
                ];
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertEquals(
            [
                '_page' => [
                    [
                        'key' => 'test',
                        'label' => 'test_label',
                        'multiple' => false,
                        'required' => false,
                        'values' => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                        'default' => null,
                        'type' => 'select',
                        'master' => false,
                        'searchable' => false,
                        'searchKeys' => ['label'],
                        'template' => '{{label}}',
                    ],
                ],
            ],
            $dashboard->dashboardConfig()['filters'],
        );
    }

    /** @test */
    public function we_can_get_dashboard_section_based_filters_config()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function getFilters(): ?array
            {
                return [
                    new class extends DashboardSelectFilter
                    {
                        public function values(): array
                        {
                            return [1 => 'A', 2 => 'B'];
                        }
                    },
                    'section-1' => [
                        new class extends DashboardSelectFilter
                        {
                            public function values(): array
                            {
                                return [3 => 'C', 4 => 'D'];
                            }
                        },
                    ]
                ];
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertEquals(
            [
                ['id' => 1, 'label' => 'A'],
                ['id' => 2, 'label' => 'B'],
            ],
            $dashboard->dashboardConfig()['filters']['_page'][0]['values'],
        );

        $this->assertEquals(
            [
                ['id' => 3, 'label' => 'C'],
                ['id' => 4, 'label' => 'D'],
            ],
            $dashboard->dashboardConfig()['filters']['section-1'][0]['values'],
        );
    }

    // Note: all other filters test already are in EntityListFilterTest
}
