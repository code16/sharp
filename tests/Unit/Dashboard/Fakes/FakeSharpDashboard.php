<?php

namespace Code16\Sharp\Tests\Unit\Dashboard\Fakes;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\SharpDashboard;

class FakeSharpDashboard extends SharpDashboard
{
    protected function buildWidgets(): void {}
    protected function buildWidgetsLayout(): void {}
    protected function buildWidgetsData(): void { }
}