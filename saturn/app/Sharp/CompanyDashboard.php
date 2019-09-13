<?php

namespace App\Sharp;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Illuminate\Support\Facades\DB;

class CompanyDashboard extends SharpDashboard
{

    function buildWidgets()
    {
        $this->addWidget(
            SharpPieGraphWidget::make("capacities_pie")
                ->setTitle("Spaceships by capacity")
        )->addWidget(
            SharpLineGraphWidget::make("capacities")
                ->setTitle("Spaceships by capacity")
        )->addWidget(
            SharpPanelWidget::make("activeSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> spaceships in activity")
                ->setLink('spaceship')
        )->addWidget(
            SharpPanelWidget::make("inactiveSpaceships")
                ->setInlineTemplate("<h1>{{count}}</h1> inactive spaceships")
        )->addWidget(
            SharpOrderedListWidget::make("topTravelledSpaceshipModels")
                ->setTitle("Top travelled spaceship types")
        );
    }

    function buildWidgetsLayout()
    {
        $this
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "capacities_pie")
                    ->addWidget(6, "capacities");
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "activeSpaceships")
                    ->addWidget(6, "inactiveSpaceships");
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "topTravelledSpaceshipModels");
            });
    }

    function buildWidgetsData(DashboardQueryParams $params)
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
                ->setColor("#3e9651")

        );

        $this->addGraphDataSet(
            "capacities",
            SharpGraphWidgetDataSet::make($capacities->map(function($value) {
                return $value * rand(1, 3);
            }))
                ->setLabel("Capacities 2")
                ->setColor("#6b4c9a")
        );


        //pie

        $this->addGraphDataSet(
            "capacities_pie",
            SharpGraphWidgetDataSet::make([
                rand(1, 3)
            ])
                ->setLabel("Capacities 1")
                ->setColor("#3e9651")
        );

        $this->addGraphDataSet(
            "capacities_pie",
            SharpGraphWidgetDataSet::make([
                    rand(2, 6)
            ])
                ->setLabel("Capacities 2")
                ->setColor("#6b4c9a")
        );

        $this->addGraphDataSet(
            "capacities_pie",
            SharpGraphWidgetDataSet::make([
                rand(4, 9)
            ])
                ->setLabel("Capacities 3")
                ->setColor("#2d2d2d")
        );

        $this->setPanelData(
            "activeSpaceships", ["count" => $spaceships->where("state", "active")->first()->count]
        )->setPanelData(
            "inactiveSpaceships", ["count" => $spaceships->where("state", "inactive")->first()->count]
        );

        $this->setOrderedListData(
            "topTravelledSpaceshipModels", [
                [
                    "label" => "Adams",
                    "count" => 78,
                ],
                [
                    "label" => "Adams",
                    "url" => "/shiptype/12",
                ],
                [
                    "label" => "Quia",
                    "count" => 55,
                    "url" => "/shiptype/31",
                ],
            ]
        );
    }

}