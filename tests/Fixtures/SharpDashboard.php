<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
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

    /**
     * Return dashboard's widgets data as an array.
     *
     * @return array
     */
    protected function getWidgetsData()
    {
        return [
            "bars1" => [
                "a" => 10, "b" => 20, "c" => 30,
            ], "bars2" => [
                "a" => 10, "b" => 20, "c" => 30,
            ], "bars3" => [
                "a" => 10, "b" => 20, "c" => 30,
            ],
        ];
    }
}