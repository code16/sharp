<?php

namespace App\Sharp\Dashboard;

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
                    ->setShowLegend(false)
                    ->setDisplayHorizontalAxisAsTimeline()
            )
            ->addWidget(
                SharpFigureWidget::make('visit_count')
                    ->setTitle('Total visits'),
            )
            ->addWidget(
                SharpFigureWidget::make('page_count')
                    ->setTitle('Total pageviews'),
            );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout
            ->addFullWidthWidget('visits_line')
            ->addRow(fn (DashboardLayoutRow $row) => $row
                ->addWidget(6, 'visit_count')
                ->addWidget(6, 'page_count')
            );
    }

    public function getFilters(): ?array
    {
        return [
            PeriodRequiredFilter::class,
            HiddenFilter::make('post'),
        ];
    }

    protected function buildWidgetsData(): void
    {
        $visitCount = $this->getStartDate()->diffInDays($this->getEndDate()) * rand(10, 100);

        $this
            ->setFigureData('visit_count', figure: $visitCount)
            ->setFigureData('page_count', figure: $visitCount * rand(2, 10))
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
