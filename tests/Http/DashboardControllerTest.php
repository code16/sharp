<?php

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutSection;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\TestDashboard;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();

    config()->set(
        'sharp.entities.stats',
        DashboardEntity::class,
    );
});

it('gets dashboard widgets, layout and data', function () {
    fakeShowFor('stats', new class extends SharpDashboard {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer
                ->addWidget(
                    SharpPanelWidget::make('panel')
                        ->setInlineTemplate('<b>test</b>')
                )
                ->addWidget(
                    SharpFigureWidget::make('figure')
                );
        }

        protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
        {
            $dashboardLayout
                ->addSection('section', function (DashboardLayoutSection $section) {
                    $section
                        ->addRow(function (DashboardLayoutRow $row) {
                            $row
                                ->addWidget(4, 'panel')
                                ->addWidget(8, 'figure');
                        });
                });
        }

        protected function buildWidgetsData(): void
        {
            $this
                ->setPanelData('panel', ['name' => 'Albert Einstein'])
                ->setFigureData('figure', 200, '€', '+3%');
        }
    });

    $this->withoutExceptionHandling();

    $this->get('/sharp/s-dashboard/stats')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('dashboard', fn (Assert $dashboard) => $dashboard
                ->where('layout.sections.0.rows.0.0.key', 'panel')
                ->where('layout.sections.0.rows.0.0.size', 4)
                ->where('layout.sections.0.rows.0.1.key', 'figure')
                ->where('layout.sections.0.rows.0.1.size', 8)
                ->where('widgets.panel.key', 'panel')
                ->where('widgets.figure.key', 'figure')
                ->where('data.panel.data.name', 'Albert Einstein')
                ->has('data.figure.data', fn (Assert $figure) => $figure
                    ->where('figure', '200')
                    ->where('unit', '€')
                    ->where('evolution.value', '3%')
                )
                ->etc()
            )
        );
});

it('gets dashboard config', function () {
    fakeShowFor('stats', new class extends TestDashboard {
        public function buildDashboardConfig(): void
        {
            $this->configurePageAlert('alert');
        }
    });

    $this->get('/sharp/s-dashboard/stats')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('dashboard.config.globalMessage.fieldKey')
            ->etc()
        );
});
