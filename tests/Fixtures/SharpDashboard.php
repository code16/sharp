<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard as AbstractSharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;

class SharpDashboard extends AbstractSharpDashboard
{
    protected function buildWidgets(): void
    {
        $this->addWidget(
            SharpBarGraphWidget::make('bars')
        )->addWidget(
            SharpPanelWidget::make('panel')
                ->setInlineTemplate('<b>test</b>')
        )->addWidget(
            SharpBarGraphWidget::make('bars2')
        );
    }

    protected function buildWidgetsLayout(): void
    {
        $this->addFullWidthWidget('bars')
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(4, 'panel')
                    ->addWidget(8, 'bars2');
            });
    }

    protected function buildWidgetsData(DashboardQueryParams $params): void
    {
        $this->addGraphDataSet(
            'bars1',
            SharpGraphWidgetDataSet::make(['a' => 10, 'b' => 20, 'c' => 30])
                ->setLabel('Bars 1')
        )->addGraphDataSet(
            'bars2',
            SharpGraphWidgetDataSet::make(['a' => 10, 'b' => 20, 'c' => 30])
                ->setLabel('Bars 2')
        )->setPanelData(
            'panel',
            ['name' => 'John Wayne']
        );
    }
}
