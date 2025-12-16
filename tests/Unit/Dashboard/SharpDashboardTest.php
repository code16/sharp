<?php

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutSection;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

it('returns widgets', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(
                SharpBarGraphWidget::make('widget'),
            );
        }
    };

    expect($dashboard->widgets())->toEqual([
        'widget' => [
            'key' => 'widget',
            'type' => 'graph',
            'display' => 'bar',
            'ratioX' => 16,
            'ratioY' => 9,
            'minimal' => false,
            'showLegend' => true,
            'dateLabels' => false,
            'options' => [
                'horizontal' => false,
            ],
        ],
    ]);
});

it('allows to build a layout without section', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer
                ->addWidget(SharpBarGraphWidget::make('widget'))
                ->addWidget(SharpBarGraphWidget::make('widget2'))
                ->addWidget(SharpBarGraphWidget::make('widget3'));
        }

        protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
        {
            $dashboardLayout
                ->addFullWidthWidget('widget')
                ->addRow(function (DashboardLayoutRow $row) {
                    $row->addWidget(4, 'widget2')
                        ->addWidget(8, 'widget3');
                });
        }
    };

    expect($dashboard->widgetsLayout())
        ->toEqual([
            'sections' => [
                [
                    'key' => null,
                    'title' => '',
                    'rows' => [
                        [
                            ['key' => 'widget', 'size' => 12],
                        ],
                        [
                            ['key' => 'widget2', 'size' => 4],
                            ['key' => 'widget3', 'size' => 8],
                        ],
                    ],
                ],
            ],
        ]);
});

it('allows to build a layout with sections', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer
                ->addWidget(SharpBarGraphWidget::make('widget'))
                ->addWidget(SharpBarGraphWidget::make('widget2'))
                ->addWidget(SharpBarGraphWidget::make('widget3'));
        }

        protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
        {
            $dashboardLayout
                ->addSection('section1', function (DashboardLayoutSection $section) {
                    $section
                        ->setKey('section1-key')
                        ->addRow(function (DashboardLayoutRow $row) {
                            $row->addWidget(4, 'widget2')
                                ->addWidget(8, 'widget3');
                        });
                })
                ->addSection('section2', function (DashboardLayoutSection $section) {
                    $section
                        ->setKey('section2-key')
                        ->addFullWidthWidget('widget');
                });
        }
    };

    expect($dashboard->widgetsLayout())
        ->toEqual([
            'sections' => [
                [
                    'key' => 'section1-key',
                    'title' => 'section1',
                    'rows' => [
                        [
                            ['key' => 'widget2', 'size' => 4],
                            ['key' => 'widget3', 'size' => 8],
                        ],
                    ],
                ], [
                    'key' => 'section2-key',
                    'title' => 'section2',
                    'rows' => [
                        [
                            ['key' => 'widget', 'size' => 12],
                        ],
                    ],
                ],
            ],
        ]);
});

it('handles graph widget data', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(SharpBarGraphWidget::make('widget'));
        }

        protected function buildWidgetsData(): void
        {
            $this->addGraphDataSet(
                'widget',
                SharpGraphWidgetDataSet::make([
                    'a' => 10,
                    'b' => 20,
                    'c' => 30,
                ])->setLabel('test')->setColor('blue'));
        }
    };

    expect($dashboard->data())
        ->toEqual([
            'widget' => [
                'key' => 'widget',
                'datasets' => [
                    [
                        'data' => [10, 20, 30],
                        'label' => 'test',
                        'color' => 'blue',
                    ],
                ],
                'labels' => [
                    'a',
                    'b',
                    'c',
                ],
            ],
        ]);
});

it('handles graph widget data with multiple datasets', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(SharpBarGraphWidget::make('widget'));
        }

        protected function buildWidgetsData(): void
        {
            $this->addGraphDataSet('widget',
                SharpGraphWidgetDataSet::make([
                    'a' => 10,
                    'b' => 20,
                    'c' => 30,
                ])->setLabel('test')->setColor('blue'));
            $this->addGraphDataSet('widget',
                SharpGraphWidgetDataSet::make([
                    'a' => 40,
                    'b' => 50,
                    'c' => 60,
                ])->setLabel('test2')->setColor('red'));
        }
    };

    expect($dashboard->data())
        ->toEqual([
            'widget' => [
                'key' => 'widget',
                'datasets' => [
                    [
                        'data' => [10, 20, 30],
                        'label' => 'test',
                        'color' => 'blue',
                    ],
                    [
                        'data' => [40, 50, 60],
                        'label' => 'test2',
                        'color' => 'red',
                    ],
                ],
                'labels' => [
                    'a',
                    'b',
                    'c',
                ],
            ],
        ]);
});

it('handles panel widget data', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(
                SharpPanelWidget::make('widget')->setTemplate('<b>Hello {{ $user }}</b>'),
            );
        }

        protected function buildWidgetsData(): void
        {
            $this->setPanelData('widget', ['user' => 'John Wayne']);
        }
    };

    expect($dashboard->data())
        ->toEqual([
            'widget' => [
                'key' => 'widget',
                'data' => [
                    'user' => 'John Wayne',
                ],
                'html' => '<b>Hello John Wayne</b>',
            ],
        ]);
});

it('handles ordered list widget data', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(SharpOrderedListWidget::make('widget'));
        }

        protected function buildWidgetsData(): void
        {
            $this->setOrderedListData('widget', [
                [
                    'label' => 'John Wayne',
                    'count' => 888,
                ],
                [
                    'label' => 'Toto',
                    'count' => 771,
                ],
            ]);
        }
    };

    // Have to manually call this to ensure widgets are loaded
    $dashboard->widgets();

    expect($dashboard->data())
        ->toEqual([
            'widget' => [
                'key' => 'widget',
                'data' => [
                    [
                        'label' => 'John Wayne',
                        'count' => 888,
                        'url' => null,
                    ],
                    [
                        'label' => 'Toto',
                        'count' => 771,
                        'url' => null,
                    ],
                ],
            ],
        ]);
});

it('handles ordered list widget item url', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer->addWidget(
                SharpOrderedListWidget::make('widget')
                    ->buildItemLink(function ($item) {
                        return $item['id'] == 3
                            ? null
                            : LinkToEntityList::make('my-entity')
                                ->addFilter('type', $item['id']);
                    }),
            );
        }

        protected function buildWidgetsData(): void
        {
            $this->setOrderedListData('widget', [
                [
                    'id' => 1,
                    'label' => 'John Wayne',
                    'count' => 888,
                ],
                [
                    'id' => 2,
                    'label' => 'Jane Wayne',
                    'count' => 771,
                ],
                [
                    'id' => 3,
                    'label' => 'John Ford',
                    'count' => 112,
                ],
            ]);
        }
    };

    // Have to manually call this to ensure widgets are loaded
    $dashboard->widgets();

    expect($dashboard->data())
        ->toEqual([
            'widget' => [
                'key' => 'widget',
                'data' => [
                    [
                        'id' => 1,
                        'label' => 'John Wayne',
                        'count' => 888,
                        'url' => 'http://localhost/sharp/root/s-list/my-entity?filter_type=1',
                    ],
                    [
                        'id' => 2,
                        'label' => 'Jane Wayne',
                        'count' => 771,
                        'url' => 'http://localhost/sharp/root/s-list/my-entity?filter_type=2',
                    ],
                    [
                        'id' => 3,
                        'label' => 'John Ford',
                        'count' => 112,
                        'url' => null,
                    ],
                ],
            ],
        ]);
});

it('allows to configure a page alert', function () {
    $dashboard = new class() extends FakeSharpDashboard
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelWarning()
                ->setMessage('My page alert')
                ->setButton('My button', 'https://example.com');
        }
    };

    expect($dashboard->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => PageAlertLevel::Warning,
            'buttonLabel' => 'My button',
            'buttonUrl' => 'https://example.com',
        ]);
});
