<?php

namespace Code16\Sharp\Tests\Fixtures\Sharp;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;

class TestDashboard extends SharpDashboard
{
    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer->addWidget(
            SharpPanelWidget::make('panel')
                ->setInlineTemplate('<b>test {{name}}</b>')
        );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout->addFullWidthWidget('panel');
    }

    protected function buildWidgetsData(): void
    {
        $this->setPanelData(
            'panel',
            ['name' => 'Albert Einstein']
        );
    }
}