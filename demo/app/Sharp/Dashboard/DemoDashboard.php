<?php

namespace App\Sharp\Dashboard;

use App\Models\Category;
use App\Models\User;
use App\Sharp\Dashboard\Commands\ExportStatsAsCsvCommand;
use App\Sharp\Utils\Filters\PeriodRequiredFilter;
use App\Sharp\Utils\Filters\StateFilter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DemoDashboard extends SharpDashboard
{
    private static array $colors = [
        '#7F1D1D',
        '#F472B6',
        '#6366F1',
        '#10B981',
        '#F59E0B',
        '#3B82F6',
        '#064E3B',
        '#EC4899',
        '#78350F',
        '#9CA3AF',
    ];
    private static int $colorsIndex = 0;

    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer
            ->addWidget(
                SharpBarGraphWidget::make('authors_bar')
                    ->setTitle('Posts by author')
                    ->setShowLegend(false)
                    ->setHorizontal(),
            )
            ->addWidget(
                SharpPieGraphWidget::make('categories_pie')
                    ->setTitle('Posts by category'),
            )
            ->addWidget(
                SharpLineGraphWidget::make('visits_line')
                    ->setTitle('Visits')
                    ->setHeight(200)
                    ->setShowLegend()
                    ->setMinimal()
                    ->setCurvedLines(),
            )
            ->addWidget(
                SharpPanelWidget::make('draft_panel')
                    ->setInlineTemplate('<h1>{{count}}</h1> draft post(s)')
                    ->setLink(LinkToEntityList::make('posts')->addFilter(StateFilter::class, 'draft')),
            )
            ->addWidget(
                SharpPanelWidget::make('online_panel')
                    ->setInlineTemplate('<h1>{{count}}</h1> online post(s)')
                    ->setLink(LinkToEntityList::make('posts')->addFilter(StateFilter::class, 'online')),
            );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'authors_bar')
                    ->addWidget(6, 'categories_pie');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(12, 'visits_line');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'draft_panel')
                    ->addWidget(6, 'online_panel');
            });
    }

    public function getFilters(): ?array
    {
        return [
            PeriodRequiredFilter::class,
        ];
    }

    public function getDashboardCommands(): ?array
    {
        return [
            ExportStatsAsCsvCommand::class,
        ];
    }

    public function buildDashboardConfig(): void
    {
        $this->configurePageAlert(
            'Graphs below are delimited by period {{period}} (and yes, visits figures are randomly generated)',
            static::$pageAlertLevelSecondary,
        );
    }

    protected function buildWidgetsData(): void
    {
        $this->setPieGraphDataSet();
        $this->setBarsGraphDataSet();
        $this->setLineGraphDataSet();

        $posts = DB::table('posts')
            ->select(DB::raw('state, count(*) as count'))
            ->groupBy('state')
            ->get();

        $this
            ->setPanelData(
                'draft_panel', ['count' => $posts->where('state', 'draft')->first()->count ?? 0],
            )
            ->setPanelData(
                'online_panel', ['count' => $posts->where('state', 'online')->first()->count ?? 0],
            )
            ->setPageAlertData([
                'period' => sprintf(
                    '%s - %s',
                    $this->getQueryParams()->filterFor(PeriodRequiredFilter::class)['start']->isoFormat('L'),
                    $this->getQueryParams()->filterFor(PeriodRequiredFilter::class)['end']->isoFormat('L'),
                ),
            ]);
    }

    protected function setLineGraphDataSet(): void
    {
        $visits = collect(CarbonPeriod::create($this->getStartDate(), $this->getEndDate()))
            ->mapWithKeys(function (Carbon $day, $k) {
                return [$day->isoFormat('L') => (int) (rand(10000, 20000) * 1.02)];
            });

        $this
            ->addGraphDataSet(
                'visits_line',
                SharpGraphWidgetDataSet::make($visits)
                    ->setLabel('Visits')
                    ->setColor(static::nextColor()),
            )
            ->addGraphDataSet(
                'visits_line',
                SharpGraphWidgetDataSet::make($visits->map(fn ($value) => (int) ($value / (rand(20, 40) / 10))))
                    ->setLabel('New')
                    ->setColor(static::nextColor()),
            );
    }

    protected function setBarsGraphDataSet()
    {
        $data = User::withCount([
            'posts' => function (Builder $query) {
                $query->whereBetween('published_at', [$this->getStartDate(), $this->getEndDate()]);
            }, ])
            ->orderBy('posts_count', 'desc')
            ->limit(8)
            ->get()
            ->pluck('posts_count', 'name');

        $this->addGraphDataSet(
            'authors_bar',
            SharpGraphWidgetDataSet::make($data)
                ->setColor(static::nextColor()),
        );
    }

    protected function setPieGraphDataSet(): void
    {
        Category::withCount([
            'posts' => function (Builder $query) {
                $query->whereBetween('published_at', [$this->getStartDate(), $this->getEndDate()]);
            }, ])
            ->limit(8)
            ->orderBy('posts_count')
            ->limit(5)
            ->get()
            ->each(function (Category $category) {
                $this->addGraphDataSet(
                    'categories_pie',
                    SharpGraphWidgetDataSet::make([$category->posts_count])
                        ->setLabel($category->name)
                        ->setColor(static::nextColor()),
                );
            });
    }

    private static function nextColor(): string
    {
        if (static::$colorsIndex >= sizeof(static::$colors)) {
            static::$colorsIndex = 0;
        }

        return static::$colors[static::$colorsIndex++];
    }

    protected function getStartDate(): Carbon
    {
        return $this->queryParams->filterFor(PeriodRequiredFilter::class)['start'];
    }

    protected function getEndDate(): Carbon
    {
        return min(
            $this->queryParams->filterFor(PeriodRequiredFilter::class)['end'],
            today()->subDay(),
        );
    }
}
