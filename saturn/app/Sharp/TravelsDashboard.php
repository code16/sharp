<?php

namespace App\Sharp;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Illuminate\Support\Facades\DB;

class TravelsDashboard extends SharpDashboard
{

    function buildWidgets()
    {
        $this->addWidget(
            SharpBarGraphWidget::make("travels")
                ->setTitle("Travels by year")
        );
    }

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("travels");
    }

    function buildWidgetsData()
    {
        $this->addGraphDataSet(
            "travels",
            SharpGraphWidgetDataSet::make(
                DB::table('travels')
                    ->select(DB::raw('year(departure_date) as label, count(*) as value'))
                    ->groupBy(DB::raw('year(departure_date)'))
                    ->get()
                    ->pluck("value", "label")
                )
                ->setLabel("Travels")
                ->setColor("red")

        );
    }

}