<?php

namespace App\Sharp\Dashboard;

use App\Sharp\Dashboard\Commands\ExportStatsAsCsvCommand;
use App\Sharp\Utils\Filters\PeriodRequiredFilter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\EntityList\Filters\HiddenFilter;

class PostDashboard extends SharpDashboard
{
    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer
            ->addWidget(
                SharpLineGraphWidget::make('visits_line')
                    ->setTitle('Visits')
                    ->setHeight(200)
                    ->setShowLegend()
                    ->setCurvedLines(),
            )
            ->addWidget(
                SharpFigureWidget::make('visits_count')
                    ->setTitle('Total visits'),
            );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(.75, 'visits_line')
                    ->addWidget(.25, 'visits_count');
            });
    }

    public function getFilters(): ?array
    {
        return [
            PeriodRequiredFilter::class,
            HiddenFilter::make('post_id'),
        ];
    }

    public function getDashboardCommands(): ?array
    {
        return [
            ExportStatsAsCsvCommand::class,
        ];
    }

    protected function buildWidgetsData(): void
    {
        $visitTotalCount = rand(10, 25000);

        $this
            ->setFigureData(
                'visits_count',
                figure: $visitTotalCount,
            )
            ->addGraphDataSet(
                'visits_line',
                SharpGraphWidgetDataSet::make(collect(CarbonPeriod::create($this->getStartDate(), $this->getEndDate()))
                    ->mapWithKeys(fn (Carbon $day, $k) => [
                        $day->isoFormat('L') => rand(10, 100),
                    ]))
                    ->setLabel('Visits')
                    ->setColor('#274754'),
            );
    }

    protected function getStartDate(): Carbon
    {
        return $this->queryParams->filterFor(PeriodRequiredFilter::class)->getStart();
    }

    protected function getEndDate(): Carbon
    {
        return min(
            $this->queryParams->filterFor(PeriodRequiredFilter::class)->getEnd(),
            today()->subDay(),
        );
    }
}
