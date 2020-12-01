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
                ->setOptions([
//                    'horizontal' => true,
                    'dateValues' => true,
                ])
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
            ->select(DB::raw("DATE_FORMAT(departure_date,'%Y-%m') as label, count(*) as value"));

        if($spaceships = $params->filterFor("spaceships")) {
            $query->whereIn("spaceship_id", (array)$spaceships);
        }

        $query->groupBy(DB::raw('label'));

        if($departurePeriodRange = $params->filterFor("period")) {
            $query->whereBetween("departure_date", [
                $departurePeriodRange['start'],
                $departurePeriodRange['end']
            ]);
        }

        $data = $query
            ->limit(10)
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