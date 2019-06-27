<?php

namespace App\Sharp;

use App\Sharp\Commands\TravelsDashboardDownloadCommand;
use App\Sharp\Filters\TravelsDashboardPeriodFilter;
use App\Sharp\Filters\TravelsDashboardSpaceshipsFilter;
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
                ->setTitle("Travel counts")
        );
    }

    function buildDashboardConfig()
    {
        $this
            ->addFilter("spaceships", TravelsDashboardSpaceshipsFilter::class)
            ->addFilter("period", TravelsDashboardPeriodFilter::class)
            ->addDashboardCommand("download", TravelsDashboardDownloadCommand::class);
    }

    function buildWidgetsLayout()
    {
        $this->addFullWidthWidget("travels");
    }

    function buildWidgetsData(DashboardQueryParams $params)
    {
        $query = DB::table('travels')
            ->select(DB::raw('year(departure_date) as label, count(*) as value'));

        if($spaceships = $params->filterFor("spaceships")){
            $query->whereIn("spaceship_id", (array)$spaceships);
        }

        $query->groupBy(DB::raw('year(departure_date)'));

        if($departurePeriodRange = $params->filterFor("period")){
            $query->whereBetween("departure_date", [
                $departurePeriodRange['start'],
                $departurePeriodRange['end']
            ]);
        }

        $data = $query
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