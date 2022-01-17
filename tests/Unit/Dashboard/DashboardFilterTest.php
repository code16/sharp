<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Filters\DashboardDateRangeFilter;
use Code16\Sharp\Dashboard\Filters\DashboardSelectFilter;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;

class DashboardFilterTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_dashboard_filters_config()
    {
        $dashboard = new class() extends FakeSharpDashboard {
            public function getFilters(): ?array
            {
                return [
                    new class() extends DashboardSelectFilter {
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

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key'      => 'test',
                        'label'    => 'test_label',
                        'multiple' => false,
                        'required' => false,
                        'values'   => [
                            ['id' => 1, 'label' => 'A'],
                            ['id' => 2, 'label' => 'B'],
                        ],
                    ],
                ],
            ],
            $dashboard->dashboardConfig()
        );
    }

    /** @test */
    public function we_can_get_dashboard_date_range_filter_config()
    {
        $dashboard = new class() extends FakeSharpDashboard {
            public function getFilters(): ?array
            {
                return [
                    new class() extends DashboardDateRangeFilter {
                        public function buildFilterConfig(): void
                        {
                            $this->configureKey('test')
                                ->configureLabel('test_label');
                        }

                        public function values(): array
                        {
                            return [];
                        }
                    },
                ];
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertArraySubset(
            [
                'filters' => [
                    [
                        'key'      => 'test',
                        'type'     => 'daterange',
                        'label'    => 'test_label',
                        'required' => false,
                    ],
                ],
            ],
            $dashboard->dashboardConfig()
        );
    }

    // Note: all other filters test already are in EntityListFilterTest
}
