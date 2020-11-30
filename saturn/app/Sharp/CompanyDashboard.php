<?php

namespace App\Sharp;

use App\SpaceshipType;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Utils\LinkToEntity;
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
                ->setOptions([
                    'height' => 200,
                    'showLegend' => false,
                    'minimal' => true,
                ])
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
                ->buildItemLink(function(LinkToEntity $link, $item) {
                    if($item['id'] >= 5) {
                        return null;
                    }
                    return $link
                        ->setEntityKey("spaceship")
                        ->addFilter("type", $item['id']);
                })
        );
    }

    function buildWidgetsLayout()
    {
        $this
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, "capacities_pie");
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(12, "capacities");
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
            "topTravelledSpaceshipModels",
            SpaceshipType::inRandomOrder()
                ->take(5)
                ->get()
                ->map(function(SpaceshipType $type) {
                    return [
                        "id" => $type->id,
                        "label" => $type->label,
                        "count" => $type->id >= 5 ? null : rand(20, 100),
                    ];
                })
                ->sortByDesc("count")
                ->values()
                ->all()
        );
    }

}