<?php

namespace App\Sharp;

use App\Sharp\Filters\TravelsDashboardPeriodFilter;
use Code16\Sharp\Dashboard\DashboardQueryParams;
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

    function buildDashboardConfig()
    {
        $this->addFilter(
            "period",
            TravelsDashboardPeriodFilter::class
        );
    }

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("travels");
    }

    function buildWidgetsData(DashboardQueryParams $params)
    {
        $data = DB::table('travels')
            ->select(DB::raw('year(departure_date) as label, count(*) as value'))
            ->groupBy(DB::raw('year(departure_date)'))
            ->whereBetween("departure_date", [
                now()->startOfYear()->subYears($params->filterFor("period")),
                now()->endOfYear()->addYears($params->filterFor("period"))
            ])
            ->get()
            ->pluck("value", "label");

        $this->addGraphDataSet(
            "travels",
            SharpGraphWidgetDataSet::make($data)
                ->setLabel("Travels")
                ->setColor("red")
        );
    }
}