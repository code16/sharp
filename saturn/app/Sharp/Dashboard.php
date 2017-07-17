<?php

namespace App\Sharp;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Illuminate\Support\Facades\DB;

class Dashboard extends SharpDashboard
{

    function buildWidgets()
    {
        $this->addWidget(
            SharpBarGraphWidget::make("capacities")
                ->setTitle("Spaceships by capacity")
        );
    }

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("capacities");
    }

    function getWidgetsData()
    {
        $capacities = DB::table('spaceships')
            ->select(
                DB::raw('ceil(capacity/10000) as label, count(*) as count')
            )
            ->groupBy('label')
            ->get()
            ->map(function($capacity) {
                $capacity->label = "<" . ($capacity->label*10) . "k";
                return $capacity;
            });

        return [
            "capacities" => $capacities
        ];
    }
}