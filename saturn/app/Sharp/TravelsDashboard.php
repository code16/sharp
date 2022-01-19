<?php

namespace App\Sharp;

use App\Sharp\Commands\TravelsDashboardDownloadCommand;
use App\Sharp\Filters\TravelsDashboardPeriodFilter;
use App\Sharp\Filters\TravelsDashboardSpaceshipsFilter;
use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Illuminate\Support\Facades\DB;

class TravelsDashboard extends SharpDashboard
{
    public function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer->addWidget(
            SharpBarGraphWidget::make('travels')
                ->setDisplayHorizontalAxisAsTimeline()
                ->setTitle('Travel counts '.($this->queryParams->filterFor('period') ? '(period filtered)' : '')),
        );
    }

    public function getDashboardCommands(): ?array
    {
        return [
            TravelsDashboardDownloadCommand::class,
        ];
    }

    public function getFilters(): array
    {
        return [
            TravelsDashboardSpaceshipsFilter::class,
            TravelsDashboardPeriodFilter::class,
        ];
    }

    public function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout->addFullWidthWidget('travels');
    }

    public function buildWidgetsData(): void
    {
        $query = DB::table('travels')
            ->select(DB::raw("DATE_FORMAT(departure_date,'%Y-%m') as label, count(*) as value"));

        if ($spaceships = $this->queryParams->filterFor(TravelsDashboardSpaceshipsFilter::class)) {
            $query->whereIn('spaceship_id', (array) $spaceships);
        }

        $query->groupBy(DB::raw('label'));

        if ($departurePeriodRange = $this->queryParams->filterFor(TravelsDashboardPeriodFilter::class)) {
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
