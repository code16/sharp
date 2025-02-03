<?php

use Code16\Sharp\Dashboard\Filters\DashboardSelectFilter;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;

it('handles dashboard filters config', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends DashboardSelectFilter
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

    expect($dashboard->dashboardConfig()['filters'])
        ->toEqual([
            '_root' => [
                [
                    'key' => 'test',
                    'label' => 'test_label',
                    'multiple' => false,
                    'required' => false,
                    'values' => [
                        ['id' => 1, 'label' => 'A'],
                        ['id' => 2, 'label' => 'B'],
                    ],
                    'type' => 'select',
                    'master' => false,
                    'searchable' => false,
                    'searchKeys' => ['label'],
                ],
            ],
        ]);
});

it('handles dashboard section based filters config', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends DashboardSelectFilter
                {
                    public function values(): array
                    {
                        return [1 => 'A', 2 => 'B'];
                    }
                },
                'section-1' => [
                    new class() extends DashboardSelectFilter
                    {
                        public function values(): array
                        {
                            return [3 => 'C', 4 => 'D'];
                        }
                    },
                ],
            ];
        }
    };

    $dashboard->buildDashboardConfig();

    expect($dashboard->dashboardConfig()['filters']['_root'][0]['values'])
        ->toEqual([
            ['id' => 1, 'label' => 'A'],
            ['id' => 2, 'label' => 'B'],
        ])
        ->and($dashboard->dashboardConfig()['filters']['section-1'][0]['values'])
        ->toEqual([
            ['id' => 3, 'label' => 'C'],
            ['id' => 4, 'label' => 'D'],
        ]);
});

// Note: all other filters test already are in SharpEntityListFilterTest
