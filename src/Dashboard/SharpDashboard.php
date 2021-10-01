<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpWidget;
use Code16\Sharp\EntityList\Traits\HandleDashboardCommands;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Illuminate\Support\Arr;

abstract class SharpDashboard
{
    use HandleFilters, 
        HandleDashboardCommands;

    protected bool $dashboardBuilt = false;
    protected bool $layoutBuilt = false;
    protected array $widgets = [];
    protected array $graphWidgetDataSets = [];
    protected array $panelWidgetsData = [];
    protected array $orderedListWidgetsData = [];
    protected array $rows = [];
    protected ?DashboardQueryParams $queryParams;

    public final function init(): self
    {
        $this->putRetainedFilterValuesInSession();

        $this->queryParams = DashboardQueryParams::create()
            ->fillWithRequest()
            ->setDefaultFilters($this->getFilterDefaultValues());
        
        return $this;
    }

    public function getQueryParams(): ?DashboardQueryParams
    {
        return $this->queryParams;
    }

    /**
     * Add a widget.
     *
     * @param SharpWidget $widget
     * @return $this
     */
    protected function addWidget(SharpWidget $widget): self
    {
        $this->widgets[] = $widget;
        $this->dashboardBuilt = false;

        return $this;
    }

    /**
     * Add a new row with a single widget.
     *
     * @param string $widgetKey
     * @return $this
     */
    protected function addFullWidthWidget(string $widgetKey): self
    {
        $this->layoutBuilt = false;

        $this->addRow(function(DashboardLayoutRow $row) use ($widgetKey) {
            $row->addWidget(12, $widgetKey);
        });

        return $this;
    }

    /**
     * Add a new row.
     *
     * @param \Closure $callback
     * @return $this
     */
    protected function addRow(\Closure $callback): self
    {
        $row = new DashboardLayoutRow();

        $callback($row);

        $this->rows[] = $row;

        return $this;
    }

    public function widgets(): array
    {
        $this->checkDashboardIsBuilt();

        return collect($this->widgets)
            ->map->toArray()
            ->keyBy("key")
            ->all();
    }

    /**
     * Return the dashboard widgets layout.
     *
     * @return array
     */
    function widgetsLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildWidgetsLayout();
            $this->layoutBuilt = true;
        }

        return [
            "rows" => collect($this->rows)
                ->map->toArray()
                ->toArray()
        ];
    }

    /**
     * Build config, meaning add filters, if necessary.
     */
    public function buildDashboardConfig(): void
    {
    }

    /**
     * Return all dashboard commands in an array of class names or instances
     */
    function getDashboardCommands(): ?array
    {
        return null;
    }

    public function dashboardConfig(): array
    {
        return tap([], function(&$config) {
            $this->appendFiltersToConfig($config);
            $this->appendDashboardCommandsToConfig($config);
        });
    }

    /**
     * Return data, as an array.
     *
     * @return array
     */
    function data(): array
    {
        $this->buildWidgetsData();
            
        // First, graph widgets dataSets
        $data = collect($this->graphWidgetDataSets)
            ->map(function(array $dataSets, string $key) {
                $dataSetsValues = collect($dataSets)->map->toArray();

                return [
                    "key" => $key,
                    "datasets" => $dataSetsValues->map(function($dataSet) {
                        return Arr::except($dataSet, "labels");
                    })->all(),
                    "labels" => $dataSetsValues->first()["labels"]
                ];
            });

        // Then, panel widgets data
        $data = $data->merge(
            collect($this->panelWidgetsData)
                ->map(function($value, $key) {
                    return [
                        "key" => $key,
                        "data" => $value
                    ];
                })
        );

        // Then, list group widgets data
        return $data
            ->merge(
                collect($this->orderedListWidgetsData)
                    ->map(function($items, $key) {
                        $widget = $this->findWidgetByKey($key);

                        $data = collect($items)
                            ->map(function($item) use($widget) {
                                return array_merge(
                                    $item,
                                    ["url" => $widget->getItemUrl($item)]
                                );
                            })
                            ->all();

                        return [
                            "key" => $key,
                            "data" => $data
                        ];
                    })
            )
            ->all();
    }

    protected function addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet): self
    {
        $this->graphWidgetDataSets[$graphWidgetKey][] = $dataSet;

        return $this;
    }

    protected function setPanelData(string $panelWidgetKey, array $data): self
    {
        $this->panelWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    protected function setOrderedListData(string $panelWidgetKey, array $data): self
    {
        $this->orderedListWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    private function checkDashboardIsBuilt(): void
    {
        if (!$this->dashboardBuilt) {
            $this->buildWidgets();
            $this->dashboardBuilt = true;
        }
    }

    private function findWidgetByKey(string $key): ?SharpWidget
    {
        return collect($this->widgets)
            ->filter(function($widget) use($key) {
                return $widget->getKey() == $key;
            })
            ->first();
    }

    /**
     * Build dashboard's widget using ->addWidget.
     */
    protected abstract function buildWidgets(): void;

    /**
     * Build dashboard's widgets layout.
     */
    protected abstract function buildWidgetsLayout(): void;

    /**
     * Build dashboard's widgets data, using ->addGraphDataSet and ->setPanelData
     */
    protected abstract function buildWidgetsData(): void;
}