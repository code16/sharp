<?php

namespace App\Sharp;

use App\Feature;
use App\SpaceshipType;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Utils\LinkToEntity;
use Illuminate\Support\Facades\DB;

class CompanyDashboard extends SharpDashboard
{
    private static $colors = [
        "#7F1D1D",
        "#F59E0B",
        "#10B981",
        "#6366F1",
        "#EC4899",
        "#3B82F6",
        "#F472B6",
        "#064E3B",
        "#78350F",
        "#9CA3AF"
    ];
    
    private static $colorsIndex = 0;

    function buildWidgets()
    {
        $this
            ->addWidget(
                SharpBarGraphWidget::make("features_bars")
                    ->setTitle("Spaceships by feature")
//                    ->setHorizontal()
            )->addWidget(
                SharpPieGraphWidget::make("types_pie")
                    ->setTitle("Spaceships by type")
            )->addWidget(
                SharpLineGraphWidget::make("capacities")
                    ->setTitle("Spaceships by capacity")
                    ->setHeight(200)
                    ->setShowLegend()
                    ->setMinimal()
//                    ->setDateValues()
//                    ->setCurvedLines()
//                    ->setOptions([
//                        'height' => 200,
//    //                    'showLegend' => true,
//                        'minimal' => true,
//                        'curved' => true,
//                    ])
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
                $row->addWidget(6, "types_pie")
                    ->addWidget(6, "features_bars");
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
        $this->setLineGraphDataSet();
        $this->setPieGraphDataSet();
        $this->setBarsGraphDataSet();

        $spaceships = DB::table('spaceships')
            ->select(DB::raw('state, count(*) as count'))
            ->groupBy('state')
            ->get();

        $this
            ->setPanelData(
                "activeSpaceships", ["count" => $spaceships->where("state", "active")->first()->count]
            )
            ->setPanelData(
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

    public function setLineGraphDataSet(): void
    {
        $capacities = DB::table('spaceships')
            ->select(DB::raw('ceil(capacity/10000) as label, count(*) as value'))
            ->groupBy('label')
            ->get()
            ->map(function($capacity) {
                $capacity->label = "<" . ($capacity->label*10) . "k";
                return $capacity;
            })
            ->pluck("value", "label");
        
        $this->addGraphDataSet(
            "capacities",
            SharpGraphWidgetDataSet::make($capacities)
                ->setLabel("Capacities")
                ->setColor(static::nextColor())
        );

        $this->addGraphDataSet(
            "capacities",
            SharpGraphWidgetDataSet::make($capacities->map(function ($value) {
                return $value * rand(1, 3);
            }))
                ->setLabel("Something else")
                ->setColor(static::nextColor())
        );
    }

    public function setPieGraphDataSet(): void
    {
        $counts = DB::table('spaceships')
            ->select(DB::raw('type_id, count(*) as count'))
            ->groupBy('type_id')
            ->get();
        
        SpaceshipType::whereIn("id", $counts->pluck("type_id"))
            ->each(function(SpaceshipType $type) use($counts) {
                $this->addGraphDataSet(
                    "types_pie",
                    SharpGraphWidgetDataSet::make([
                        $counts->where("type_id", $type->id)->first()->count
                    ])
                        ->setLabel($type->label)
                        ->setColor(static::nextColor())
                );
            });
    }

    private function setBarsGraphDataSet()
    {
        $counts = DB::table('feature_spaceship')
            ->select(DB::raw('feature_id, count(*) as count'))
            ->groupBy('feature_id')
            ->orderBy("count")
            ->limit(8)
            ->get();
        
        Feature::whereIn("id", $counts->pluck("feature_id"))
            ->each(function(Feature $feature) use($counts) {
                $this->addGraphDataSet(
                    "features_bars",
                    SharpGraphWidgetDataSet::make([
                        $counts->where("feature_id", $feature->id)->first()->count
                    ])
                        ->setLabel($feature->name)
                        ->setColor(static::nextColor())
                );
            });
    }

    private static function nextColor(): string
    {
        if(static::$colorsIndex >= sizeof(static::$colors)) {
            static::$colorsIndex = 0;
        }
        
        return static::$colors[static::$colorsIndex++];
    }

}