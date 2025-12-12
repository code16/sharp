<?php

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutSection;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    sharp()->config()->declareEntity(DashboardEntity::class);
    login();
});

it('gets dashboard data as JSON in an EmbeddedDashboard case', function () {
    fakeDashboardFor(DashboardEntity::class, new class() extends SharpDashboard
    {
        protected function buildWidgets(WidgetsContainer $widgetsContainer): void
        {
            $widgetsContainer
                ->addWidget(
                    SharpPanelWidget::make('panel')
                        ->setTemplate('')
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
                                ->addWidget(.3, 'panel')
                                ->addWidget(.7, 'figure');
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

    $this
        ->getJson('/sharp/api/root/dashboard/'.DashboardEntity::$entityKey, headers: [
            SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => url('/sharp/root/s-list/person/s-show/person/1'),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.panel.data', fn (AssertableJson $json) => $json
                ->where('name', 'Albert Einstein')
                ->etc()
            )
            ->has('data.figure.data', fn (AssertableJson $json) => $json
                ->where('figure', '200')
                ->etc()
            )
            ->etc()
        );
});
