<?php

namespace App\Sharp;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Illuminate\Support\Facades\DB;

class Dashboard extends SharpDashboard
{

    function buildWidgets()
    {
        $this->addWidget(
            SharpBarGraphWidget::make("capacities")
                ->setTitle("Spaceships by capacity")
        )/*->addWidget(
            SharpPanelWidget::make("activeSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
        )->addWidget(
            SharpPanelWidget::make("inactiveSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> inactive spaceships")
        )*/;
    }

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("capacities")
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "activeSpaceships")
                    ->addWidget(6, "inactiveSpaceships");
            });
    }

    function buildWidgetsData()
    {
        $capacities = DB::table('spaceships')
            ->select(DB::raw('ceil(capacity/10000) as label, count(*) as value'))
            ->groupBy('label')
            ->get()
            ->map(function($capacity) {
                $capacity->label = "<" . ($capacity->label*10) . "k";
                return $capacity;
            })->pluck("value", "label");

        $spaceships = DB::table('spaceships')
            ->select(DB::raw('state, count(*) as count'))
            ->groupBy('state')
            ->get();

        $this->addGraphDataSet(
            "capacities",
            SharpGraphWidgetDataSet::make($capacities)
                ->setLabel("Capacities")
                ->setColor("red")
        )->addGraphDataSet(
            "capacities",
            SharpGraphWidgetDataSet::make($capacities)
                ->setLabel("Capacities")
                ->setColor("blue")
        );

//        )->setPanelData(
//            "activeSpaceships", ["count" => $spaceships->where("state", "active")->count]
//
//        )->setPanelData(
//            "inactiveSpaceships", ["count" => $spaceships->where("state", "inactive")->count]
//        );
    }

}