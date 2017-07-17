<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard as AbstractSharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;

class SharpDashboard extends AbstractSharpDashboard
{

    /**
     * Build dashboard's widget using ->addWidget.
     */
    protected function buildWidgets()
    {
        $this->addWidget(
            SharpBarGraphWidget::make("bars")
        )->addWidget(
            SharpBarGraphWidget::make("bars2")
        )->addWidget(
            SharpBarGraphWidget::make("bars3")
        );
    }

    /**
     * Build dashboard's widgets layout.
     */
    protected function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("bars")
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(4, "bars2")
                    ->addWidget(8, "bars3");
            });
    }

    protected function buildWidgetsData()
    {
        $this->addGraphDataSet(
            "bars1",
            SharpGraphWidgetDataSet::make(["a" => 10, "b" => 20, "c" => 30])
                ->setLabel("Bars 1")
        )->addGraphDataSet(
            "bars2",
            SharpGraphWidgetDataSet::make(["a" => 10, "b" => 20, "c" => 30])
                ->setLabel("Bars 2")
        )->addGraphDataSet(
            "bars3",
            SharpGraphWidgetDataSet::make(["a" => 10, "b" => 20, "c" => 30])
                ->setLabel("Bars 3")
        );
    }
}