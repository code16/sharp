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
    public function buildWidgets(): void
    {
        $this->addWidget(
            SharpBarGraphWidget::make('travels')
                ->setDisplayHorizontalAxisAsTimeline()
                ->setTitle('Travel counts'),
        );
    }

    public function buildDashboardConfig(): void
    {
        $this
            ->addFilter('spaceships', TravelsDashboardSpaceshipsFilter::class)
            ->addFilter('period', TravelsDashboardPeriodFilter::class)
            ->addDashboardCommand('download', TravelsDashboardDownloadCommand::class);
    }

    public function buildWidgetsLayout(): void
    {
        $this->addFullWidthWidget('travels');
    }

    public function buildWidgetsData(DashboardQueryParams $params): void
    {
        $query = DB::table('travels')
            ->select(DB::raw("DATE_FORMAT(departure_date,'%Y-%m') as label, count(*) as value"));

        if ($spaceships = $params->filterFor('spaceships')) {
            $query->whereIn('spaceship_id', (array) $spaceships);
        }

        $query->groupBy(DB::raw('label'));

        if ($departurePeriodRange = $params->filterFor('period')) {
            $query->whereBetween('departure_date', [
                $departurePeriodRange['start'],
                $departurePeriodRange['end'],
            ]);
        }

        $data = $query
            ->get()
            ->pluck('value', 'label');

        $this->addGraphDataSet(
            'travels',
            SharpGraphWidgetDataSet::make($data)
                ->setLabel('Travels')
                ->setColor('red'),
        );
    }
}
