<?php

namespace App\Sharp;

use App\Feature;
use App\SpaceshipType;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Illuminate\Support\Facades\DB;

class CompanyDashboard extends SharpDashboard
{
    private static $colors = [
        '#7F1D1D',
        '#F59E0B',
        '#10B981',
        '#6366F1',
        '#EC4899',
        '#3B82F6',
        '#F472B6',
        '#064E3B',
        '#78350F',
        '#9CA3AF',
    ];

    private static $colorsIndex = 0;

    public function buildWidgets(): void
    {
        $this
            ->addWidget(
                SharpBarGraphWidget::make('features_bars')
                    ->setTitle('Main features')
                    ->setShowLegend(false)
                    ->setHorizontal(),
            )->addWidget(
                SharpPieGraphWidget::make('types_pie')
                    ->setTitle('Spaceships by type'),
            )->addWidget(
                SharpLineGraphWidget::make('capacities')
                    ->setTitle('Spaceships by capacity')
                    ->setHeight(200)
                    ->setShowLegend()
                    ->setMinimal()
                    ->setCurvedLines(),
            )->addWidget(
                SharpPanelWidget::make('activeSpaceships')
                    ->setInlineTemplate('<h1>{{count}}</h1> spaceships in activity')
                    ->setLink(LinkToEntityList::make('spaceship')),
            )->addWidget(
                SharpPanelWidget::make('inactiveSpaceships')
                    ->setInlineTemplate('<h1>{{count}}</h1> inactive spaceships'),
            )->addWidget(
                SharpOrderedListWidget::make('topTravelledSpaceshipModels')
                    ->setTitle('Top travelled spaceship types')
                    ->setHtml()
                    ->buildItemLink(function ($item) {
                        if ($item['id'] >= 5) {
                            return null;
                        }

                        return LinkToEntityList::make('spaceship')
                            ->addFilter('type', $item['id']);
                    }),
            );
    }

    public function buildWidgetsLayout(): void
    {
        $this
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'types_pie')
                    ->addWidget(6, 'features_bars');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(12, 'capacities');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'activeSpaceships')
                    ->addWidget(6, 'inactiveSpaceships');
            })
            ->addRow(function (DashboardLayoutRow $row) {
                $row->addWidget(6, 'topTravelledSpaceshipModels');
            });
    }

    public function buildWidgetsData(DashboardQueryParams $params): void
    {
        $this->setPieGraphDataSet();
        $this->setBarsGraphDataSet();
        $this->setLineGraphDataSet();

        $spaceships = DB::table('spaceships')
            ->select(DB::raw('state, count(*) as count'))
            ->groupBy('state')
            ->get();

        $this
            ->setPanelData(
                'activeSpaceships',
                ['count' => $spaceships->where('state', 'active')->first()->count],
            )
            ->setPanelData(
                'inactiveSpaceships',
                ['count' => $spaceships->where('state', 'inactive')->first()->count],
            );

        $this->setOrderedListData(
            'topTravelledSpaceshipModels',
            SpaceshipType::inRandomOrder()
                ->take(5)
                ->get()
                ->map(function (SpaceshipType $type) {
                    return [
                        'id' => $type->id,
                        'label' => sprintf('<em>%s</em>', $type->label),
                        'count' => $type->id >= 5 ? null : rand(20, 100),
                    ];
                })
                ->sortByDesc('count')
                ->values()
                ->all(),
        );
    }

    public function setLineGraphDataSet(): void
    {
        $capacities = DB::table('spaceships')
            ->select(DB::raw('ceil(capacity/10000) as label, count(*) as value'))
            ->groupBy('label')
            ->get()
            ->map(function ($capacity) {
                $capacity->label = '<'.($capacity->label * 10).'k';

                return $capacity;
            })
            ->pluck('value', 'label');

        $this->addGraphDataSet(
            'capacities',
            SharpGraphWidgetDataSet::make($capacities)
                ->setLabel('Spaceships')
                ->setColor(static::nextColor()),
        );

        $this->addGraphDataSet(
            'capacities',
            SharpGraphWidgetDataSet::make(
                $capacities->map(function ($value) {
                    return $value * rand(1, 3);
                }),
            )
                ->setLabel('Something else')
                ->setColor(static::nextColor()),
        );
    }

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

    private function setBarsGraphDataSet()
    {
        $counts = DB::table('feature_spaceship')
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

    private static function nextColor(): string
    {
        if (static::$colorsIndex >= sizeof(static::$colors)) {
            static::$colorsIndex = 0;
        }

        return static::$colors[static::$colorsIndex++];
    }
}
