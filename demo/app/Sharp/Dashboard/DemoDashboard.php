<?php

namespace App\Sharp\Dashboard;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Illuminate\Support\Facades\DB;

class DemoDashboard extends SharpDashboard
{
    private static array $colors = [
        '#7F1D1D',
        '#F472B6',
        '#EC4899',
        '#6366F1',
        '#10B981',
        '#F59E0B',
        '#3B82F6',
        '#064E3B',
        '#78350F',
        '#9CA3AF',
    ];
    private static int $colorsIndex = 0;

    public function setPieGraphDataSet(): void
    {
        $counts = DB::table('spaceships')
            ->select(DB::raw('type_id, count(*) as count'))
            ->groupBy('type_id')
            ->get();

        SpaceshipType::whereIn('id', $counts->pluck('type_id'))
            ->each(function (SpaceshipType $type) use ($counts) {
                $this->addGraphDataSet(
                    'types_pie',
                    SharpGraphWidgetDataSet::make([
                        $counts->where('type_id', $type->id)->first()->count,
                    ])
                        ->setLabel($type->label)
                        ->setColor(static::nextColor()),
                );
            });
    }

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
            );
    }

    protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void
    {
        $dashboardLayout
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(12, 'visits_line');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'authors_bar')
                    ->addWidget(6, 'categories_pie');
            });
    }

    protected function buildWidgetsData(): void
    {
        $this->setLineGraphDataSet();
        $this->setBarsGraphDataSet();
//        $this->setPieGraphDataSet();
    }

    public function setLineGraphDataSet(): void
    {
        $visits =  collect(CarbonPeriod::create(today()->subDays(30), today()->subDay()))
            ->mapWithKeys(function(Carbon $day, $k) {
                return [$day->isoFormat('L') => (int)(rand(10000, 20000) * 1.02)];
            });
        
        $this->addGraphDataSet(
            'visits_line',
            SharpGraphWidgetDataSet::make($visits)
                ->setLabel('Visits')
                ->setColor(static::nextColor()),
        );

        $this->addGraphDataSet(
            'visits_line',
            SharpGraphWidgetDataSet::make($visits->map(fn ($value) => (int) ($value / (rand(20, 40) / 10))))
                ->setLabel('New')
                ->setColor(static::nextColor()),
        );
    }

    private static function nextColor(): string
    {
        if (static::$colorsIndex >= sizeof(static::$colors)) {
            static::$colorsIndex = 0;
        }

        return static::$colors[static::$colorsIndex++];
    }

    private function setBarsGraphDataSet()
    {
        User::withCount('posts')
            ->select(DB::raw('feature_id, count(*) as count'))
            ->groupBy('feature_id')
            ->orderBy('count')
            ->limit(8)
            ->get();

        $data = Feature::whereIn('id', $counts->pluck('feature_id'))
            ->get()
            ->map(function (Feature $feature) use ($counts) {
                return [
                    'value' => $counts->where('feature_id', $feature->id)->first()->count,
                    'label' => $feature->name,
                ];
            })
            ->pluck('value', 'label');

        $this->addGraphDataSet(
            'features_bars',
            SharpGraphWidgetDataSet::make($data)
                ->setColor(static::nextColor()),
        );
    }
}