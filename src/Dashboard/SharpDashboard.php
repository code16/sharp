<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\EntityList\Traits\HandleDashboardCommands;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Illuminate\Support\Arr;

abstract class SharpDashboard
{
    use HandleFilters,
        HandleDashboardCommands;

    protected bool $dashboardBuilt = false;
    protected array $graphWidgetDataSets = [];
    protected array $panelWidgetsData = [];
    protected array $orderedListWidgetsData = [];
    protected ?DashboardQueryParams $queryParams;
    protected ?DashboardLayout $dashboardLayout = null;
    protected ?WidgetsContainer $widgetsContainer = null;

    public final function init(): self
    {
        $this->putRetainedFilterValuesInSession();

        $this->queryParams = DashboardQueryParams::create()
            ->fillWithRequest()
            ->setDefaultFilters($this->getFilterDefaultValues());

        return $this;
    }

    public final function getQueryParams(): ?DashboardQueryParams
    {
        return $this->queryParams;
    }

    public final function widgets(): array
    {
        $this->checkDashboardIsBuilt();

        return collect($this->widgetsContainer()->getWidgets())
            ->map->toArray()
            ->keyBy("key")
            ->all();
    }

    public final function widgetsContainer(): WidgetsContainer
    {
        if ($this->widgetsContainer === null) {
            $this->widgetsContainer = new WidgetsContainer();
        }
        return $this->widgetsContainer;
    }

    public final function widgetsLayout(): array
    {
        if ($this->dashboardLayout === null) {
            $this->dashboardLayout = new DashboardLayout();
            $this->buildDashboardLayout($this->dashboardLayout);
        }

        return $this->dashboardLayout->toArray();
    }

    /**
     * Build config id necessary
     */
    public function buildDashboardConfig(): void
    {
    }

    /**
     * Return all filters in an array of class names or instances
     */
    function getFilters(): ?array
    {
        return null;
    }

    /**
     * Return all dashboard commands in an array of class names or instances
     */
    function getDashboardCommands(): ?array
    {
        return null;
    }

    public final function dashboardConfig(): array
    {
        return tap([], function (&$config) {
            $this->appendFiltersToConfig($config);
            $this->appendDashboardCommandsToConfig($config);
        });
    }

    /**
     * Return data, as an array.
     *
     * @return array
     */
    public final function data(): array
    {
        $this->buildWidgetsData();

        // First, graph widgets dataSets
        $data = collect($this->graphWidgetDataSets)
            ->map(function (array $dataSets, string $key) {
                $dataSetsValues = collect($dataSets)->map->toArray();

                return [
                    "key" => $key,
                    "datasets" => $dataSetsValues
                        ->map(function ($dataSet) {
                            return Arr::except($dataSet, "labels");
                        })
                        ->all(),
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

    protected final function addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet): self
    {
        $this->graphWidgetDataSets[$graphWidgetKey][] = $dataSet;

        return $this;
    }

    protected final function setPanelData(string $panelWidgetKey, array $data): self
    {
        $this->panelWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    protected final function setOrderedListData(string $panelWidgetKey, array $data): self
    {
        $this->orderedListWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    private function checkDashboardIsBuilt(): void
    {
        if (!$this->dashboardBuilt) {
            $this->buildWidgets($this->widgetsContainer());
            $this->dashboardBuilt = true;
        }
    }

    private function findWidgetByKey(string $key): ?SharpWidget
    {
        return collect($this->widgetsContainer()->getWidgets())
            ->filter(function ($widget) use ($key) {
                return $widget->getKey() == $key;
            })
            ->first();
    }

    protected abstract function buildWidgets(WidgetsContainer $widgetsContainer): void;

    protected abstract function buildDashboardLayout(DashboardLayout $dashboardLayout): void;

    /**
     * Build dashboard's widgets data, using ->addGraphDataSet and ->setPanelData
     */
    protected abstract function buildWidgetsData(): void;
}