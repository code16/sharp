<?php

namespace Code16\Sharp\Tests\Unit\Dashboard\Fakes;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;

class FakeSharpDashboard extends SharpDashboard
{
    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
    }

    protected function buildWidgetsData(): void
    {
    }
}