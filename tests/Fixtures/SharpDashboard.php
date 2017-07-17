<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Dashboard\SharpDashboard as AbstractSharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;

class SharpDashboard extends AbstractSharpDashboard
{

    /**
     * Build dashboard's widget using ->addWidget.
     */
    protected function buildWidgets()
    {
        $this->addWidget(
            SharpBarGraphWidget::make("bars")
        );
    }
}