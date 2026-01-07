<?php

namespace App\Sharp\Dashboard;

use App\Models\Category;
use App\Models\User;
use App\Sharp\Dashboard\Commands\ExportStatsAsCsvCommand;
use App\Sharp\Entities\PostEntity;
use App\Sharp\Utils\Filters\CategoryFilter;
use App\Sharp\Utils\Filters\PeriodFilter;
use App\Sharp\Utils\Filters\PeriodRequiredFilter;
use App\Sharp\Utils\Filters\StateFilter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutSection;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpAreaGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\PageAlerts\PageAlert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DemoDashboard extends SharpDashboard
{
    const array COLORS = [
        'oklch(0.809 0.105 251.813)',
        'oklch(0.623 0.214 259.815)',
        'oklch(0.546 0.245 262.881)',
        'oklch(0.488 0.243 264.376)',
        'oklch(0.424 0.199 265.638)',
    ];

    protected function buildWidgets(WidgetsContainer $widgetsContainer): void
    {
        $widgetsContainer
            ->addWidget(
                SharpBarGraphWidget::make('authors_bar')
                    ->setTitle('Posts by author')
                    ->setShowLegend(false)
                // ->setHorizontal(),
            )
            ->addWidget(
                SharpPieGraphWidget::make('categories_pie')
                    ->setTitle('Posts by category'),
            )
            ->addWidget(
                SharpAreaGraphWidget::make('visits_line')
                    ->setTitle('Visits')
                    ->setHeight(200)
                    // ->setStacked()
                    // ->setShowStackTotal(label: 'Total visits')
                    // ->setShowLegend()
                    ->setDisplayHorizontalAxisAsTimeline()
                // ->setEnableHorizontalAxisLabelSampling()
                // ->setShowGradient()
                // ->setShowDots()
                // ->setMinimal()

            )
            ->addWidget(
                SharpFigureWidget::make('draft_panel')
                    ->setTitle('Draft posts')
                    ->setLink(LinkToEntityList::make(PostEntity::class)->addFilter(StateFilter::class, 'draft')),
            )
            ->addWidget(
                SharpFigureWidget::make('online_panel')
                    ->setTitle('Online posts')
                    ->setLink(LinkToEntityList::make(PostEntity::class)->addFilter(StateFilter::class, 'online')),
            )
            ->addWidget(
                SharpOrderedListWidget::make('list')
                    ->setTitle('Top 3 categories')
                    ->buildItemLink(fn (array $item) => $item['url'] ?? null)
            )
            ->addWidget(
                SharpPanelWidget::make('highlighted_post')
                    ->setTitle('On the spotlight')
                    ->setTemplate(view('sharp.templates.dashboard_ranking'))
            );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout
            ->addSection('Posts', function (DashboardLayoutSection $section) {
                $section
                    ->addRow(function (DashboardLayoutRow $row) {
                        $row->addWidget(6, 'draft_panel')
                            ->addWidget(6, 'online_panel');
                    });
            })
            ->addSection('Stats', function (DashboardLayoutSection $section) {
                $section
                    ->setKey('stats-section')
                    ->addRow(fn (DashboardLayoutRow $row) => $row
                        ->addWidget(6, 'authors_bar')
                        ->addWidget(6, 'categories_pie')
                    )
                    ->addFullWidthWidget('visits_line')
                    ->addRow(fn (DashboardLayoutRow $row) => $row
                        ->addWidget(5, 'list')
                        ->addWidget(7, 'highlighted_post')
                    );
            });
    }

    public function getFilters(): ?array
    {
        return [
            'stats-section' => [
                PeriodRequiredFilter::class,
            ],
        ];
    }

    public function getDashboardCommands(): ?array
    {
        return [
            'stats-section' => [
                ExportStatsAsCsvCommand::class,
            ],
        ];
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
        $pageAlert
            ->setLevelInfo()
            ->onSection('stats-section')
            ->setMessage(
                sprintf(
                    'Graphs below are delimited by period %s - %s (and yes, visits figures are randomly generated)',
                    $this->queryParams->filterFor(PeriodRequiredFilter::class)['start']->isoFormat('L'),
                    $this->queryParams->filterFor(PeriodRequiredFilter::class)['end']->isoFormat('L'),
                )
            );
    }

    protected function buildWidgetsData(): void
    {
        $this->setPieGraphDataSet();
        $this->setBarsGraphDataSet();
        $this->setLineGraphDataSet();
        $this->setOrderedListDataSet();
        $this->setCustomPanelDataSet();

        $posts = DB::table('posts')
            ->select(DB::raw('state, count(*) as count'))
            ->groupBy('state')
            ->get();

        $this
            ->setFigureData(
                'draft_panel',
                figure: $posts->where('state', 'draft')->first()->count ?? 0,
                evolution: '+15%',
            )
            ->setFigureData(
                'online_panel',
                figure: $posts->where('state', 'online')->first()->count ?? 0,
                unit: 'post(s)',
                evolution: '-10%',
            );
    }

    protected function setLineGraphDataSet(): void
    {
        $visits = collect(CarbonPeriod::create($this->getStartDate(), $this->getEndDate()))
            ->mapWithKeys(function (Carbon $day, $k) {
                return [$day->format('Y-m-d') => (int) (rand(10000, 20000) * 1.02)];
            });

        $this
            ->addGraphDataSet(
                'visits_line',
                SharpGraphWidgetDataSet::make($visits)
                    ->setLabel('Visits')
                    ->setColor(static::COLORS[0]),
            )
            ->addGraphDataSet(
                'visits_line',
                SharpGraphWidgetDataSet::make($visits->map(fn ($value) => (int) ($value / (rand(20, 40) / 10))))
                    ->setLabel('New')
                    ->setColor(static::COLORS[1]),
            );
    }

    protected function setBarsGraphDataSet(): void
    {
        $data = User::query()
            ->withCount([
                'posts' => fn (Builder $query) => $query
                    ->whereBetween(
                        'published_at',
                        [$this->getStartDate(), $this->getEndDate()]
                    )]
            )
            ->orderBy('posts_count', 'desc')
            ->limit(15)
            ->get()
            ->pluck('posts_count', 'name');

        $this->addGraphDataSet(
            'authors_bar',
            SharpGraphWidgetDataSet::make($data)
                ->setColor(static::COLORS[1]),
        );
    }

    protected function setPieGraphDataSet(): void
    {
        Category::query()
            ->withCount([
                'posts' => fn (Builder $query) => $query
                    ->whereBetween('published_at', [
                        $this->getStartDate(),
                        $this->getEndDate(),
                    ]),
            ])
            ->limit(5)
            ->orderBy('posts_count', 'desc')
            ->get()
            ->each(function (Category $category, $i) {
                $this->addGraphDataSet(
                    'categories_pie',
                    SharpGraphWidgetDataSet::make([$category->posts_count])
                        ->setLabel($category->name)
                        ->setColor(static::COLORS[$i % count(static::COLORS)]),
                );
            });
    }

    protected function setOrderedListDataSet(): void
    {
        $this->setOrderedListData('list',
            Category::query()
                ->withCount([
                    'posts' => fn (Builder $query) => $query
                        ->whereBetween('published_at', [
                            $this->getStartDate(),
                            $this->getEndDate(),
                        ]),
                ])
                ->orderBy('posts_count', 'desc')
                ->limit(3)
                ->get()
                ->map(fn (Category $category) => [
                    'label' => $category->name,
                    'count' => $category->posts_count,
                    'url' => LinkToEntityList::make(PostEntity::class)
                        ->addFilter(CategoryFilter::class, $category->id)
                        ->addFilter(PeriodFilter::class, sprintf('%s..%s',
                            $this->getStartDate()->format('Ymd'),
                            $this->getEndDate()->format('Ymd'),
                        )),
                ])
                ->toArray()
        );
    }

    protected function setCustomPanelDataSet(): void
    {
        $author = User::query()
            ->withWhereHas('posts', fn ($query) => $query
                ->whereBetween('published_at', [$this->getStartDate(), $this->getEndDate()])
            )
            ->inRandomOrder()
            ->first();

        if ($author) {
            $this->setPanelData('highlighted_post', [
                'author' => $author,
                'post' => $author->posts->first(),
            ]);
        }
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
