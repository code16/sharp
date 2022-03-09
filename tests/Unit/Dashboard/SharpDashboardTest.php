<?php

namespace Code16\Sharp\Tests\Unit\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Dashboard\Fakes\FakeSharpDashboard;
use Code16\Sharp\Utils\Links\LinkToEntityList;

class SharpDashboardTest extends SharpTestCase
{
    /** @test */
    public function we_can_get_widgets()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            protected function buildWidgets(WidgetsContainer $widgetsContainer): void
            {
                $widgetsContainer->addWidget(
                    SharpBarGraphWidget::make('widget'),
                );
            }
        };

        $this->assertEquals(
            [
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
            ],
            $dashboard->widgets(),
        );
    }

    /** @test */
    public function we_can_get_widgets_layout()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            protected function buildWidgets(WidgetsContainer $widgetsContainer): void
            {
                $widgetsContainer->addWidget(SharpBarGraphWidget::make('widget'))
                    ->addWidget(SharpBarGraphWidget::make('widget2'))
                    ->addWidget(SharpBarGraphWidget::make('widget3'));
            }

            protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
            {
                $dashboardLayout->addFullWidthWidget('widget')
                    ->addRow(function (DashboardLayoutRow $row) {
                        $row->addWidget(4, 'widget2')
                            ->addWidget(8, 'widget3');
                    });
            }
        };

        $this->assertEquals(
            [
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
            $dashboard->widgetsLayout(),
        );
    }

    /** @test */
    public function we_can_get_graph_widget_data()
    {
        $dashboard = new class extends FakeSharpDashboard
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
                    ])->setLabel('test')->setColor('blue'), );
            }
        };

        $this->assertEquals(
            [
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
            ],
            $dashboard->data(),
        );
    }

    /** @test */
    public function we_can_get_graph_widget_data_with_multiple_datasets()
    {
        $dashboard = new class extends FakeSharpDashboard
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
                    ])->setLabel('test')->setColor('blue'), );
                $this->addGraphDataSet('widget',
                    SharpGraphWidgetDataSet::make([
                        'a' => 40,
                        'b' => 50,
                        'c' => 60,
                    ])->setLabel('test2')->setColor('red'), );
            }
        };

        $this->assertEquals(
            [
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
            ],
            $dashboard->data(),
        );
    }

    /** @test */
    public function we_can_get_panel_widget_data()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            protected function buildWidgets(WidgetsContainer $widgetsContainer): void
            {
                $widgetsContainer->addWidget(
                    SharpPanelWidget::make('widget')->setInlineTemplate('<b>Hello {{user}}</b>'),
                );
            }

            protected function buildWidgetsData(): void
            {
                $this->setPanelData('widget', ['user' => 'John Wayne']);
            }
        };

        $this->assertEquals(
            [
                'widget' => [
                    'key' => 'widget',
                    'data' => [
                        'user' => 'John Wayne',
                    ],
                ],
            ],
            $dashboard->data(),
        );
    }

    /** @test */
    public function we_can_get_ordered_list_widget_data()
    {
        $dashboard = new class extends FakeSharpDashboard
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

        $this->assertEquals(
            [
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
            ],
            $dashboard->data(),
        );
    }

    /** @test */
    public function we_can_get_ordered_list_widget_item_url()
    {
        $dashboard = new class extends FakeSharpDashboard
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

        $this->assertEquals(
            [
                'widget' => [
                    'key' => 'widget',
                    'data' => [
                        [
                            'id' => 1,
                            'label' => 'John Wayne',
                            'count' => 888,
                            'url' => 'http://localhost/sharp/s-list/my-entity?filter_type=1',
                        ],
                        [
                            'id' => 2,
                            'label' => 'Jane Wayne',
                            'count' => 771,
                            'url' => 'http://localhost/sharp/s-list/my-entity?filter_type=2',
                        ],
                        [
                            'id' => 3,
                            'label' => 'John Ford',
                            'count' => 112,
                            'url' => null,
                        ],
                    ],
                ],
            ],
            $dashboard->data(),
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_without_data()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function buildDashboardConfig(): void
            {
                $this->configurePageAlert('template', null, 'test-key');
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertEquals('test-key', $dashboard->dashboardConfig()['globalMessage']['fieldKey']);
        $this->assertEquals(
            SharpShowHtmlField::make('test-key')->setInlineTemplate('template')->toArray(),
            $dashboard->dashboardMetaFields()['test-key'],
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_with_template_data()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function buildDashboardConfig(): void
            {
                $this->configurePageAlert('Hello {{name}}', null, 'test-key');
            }

            public function buildWidgetsData(): void
            {
                $this->setPageAlertData([
                    'name' => 'Bob',
                ]);
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertEquals(
            ['name' => 'Bob'],
            $dashboard->data()['test-key'],
        );
    }

    /** @test */
    public function we_can_configure_a_global_message_field_with_alert_level()
    {
        $dashboard = new class extends FakeSharpDashboard
        {
            public function buildDashboardConfig(): void
            {
                $this->configurePageAlert('alert', static::$pageAlertLevelDanger);
            }
        };

        $dashboard->buildDashboardConfig();

        $this->assertEquals(
            'danger',
            $dashboard->dashboardConfig()['globalMessage']['alertLevel'],
        );
    }
}
