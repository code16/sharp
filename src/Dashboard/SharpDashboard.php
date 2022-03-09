<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpWidget;
use Code16\Sharp\Dashboard\Widgets\WidgetsContainer;
use Code16\Sharp\EntityList\Traits\HandleDashboardCommands;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class SharpDashboard
{
    use HandleFilters,
        HandleDashboardCommands,
        HandlePageAlertMessage;

    protected bool $dashboardBuilt = false;
    protected array $graphWidgetDataSets = [];
    protected array $panelWidgetsData = [];
    protected array $orderedListWidgetsData = [];
    protected ?array $pageAlertData = null;
    protected ?DashboardQueryParams $queryParams;
    protected ?DashboardLayout $dashboardLayout = null;
    protected ?WidgetsContainer $widgetsContainer = null;

    final public function init(): self
    {
        $this->putRetainedFilterValuesInSession();

        $this->queryParams = DashboardQueryParams::create()
            ->fillWithRequest()
            ->setDefaultFilters($this->getFilterDefaultValues());

        return $this;
    }

    final public function getQueryParams(): ?DashboardQueryParams
    {
        return $this->queryParams;
    }

    final public function widgets(): array
    {
        $this->checkDashboardIsBuilt();

        return collect($this->widgetsContainer()->getWidgets())
            ->map->toArray()
            ->keyBy('key')
            ->all();
    }

    final public function widgetsContainer(): WidgetsContainer
    {
        if ($this->widgetsContainer === null) {
            $this->widgetsContainer = new WidgetsContainer();
        }

        return $this->widgetsContainer;
    }

    final public function widgetsLayout(): array
    {
        if ($this->dashboardLayout === null) {
            $this->dashboardLayout = new DashboardLayout();
            $this->buildDashboardLayout($this->dashboardLayout);
        }

        return $this->dashboardLayout->toArray();
    }

    /**
     * Build config if necessary.
     */
    public function buildDashboardConfig(): void
    {
    }

    /**
     * Return all filters in an array of class names or instances.
     */
    public function getFilters(): ?array
    {
        return null;
    }

    /**
     * Return all dashboard commands in an array of class names or instances.
     */
    public function getDashboardCommands(): ?array
    {
        return null;
    }

    final public function dashboardConfig(): array
    {
        return tap([], function (&$config) {
            $this->appendFiltersToConfig($config);
            $this->appendDashboardCommandsToConfig($config);
            $this->appendGlobalMessageToConfig($config);
        });
    }

    /**
     * Return data, as an array.
     *
     * @return array
     */
    final public function data(): array
    {
        $this->buildWidgetsData();

        // First, graph widgets dataSets
        $data = collect($this->graphWidgetDataSets)
            ->map(function (array $dataSets, string $key) {
                $dataSetsValues = collect($dataSets)->map->toArray();

                return [
                    'key' => $key,
                    'datasets' => $dataSetsValues
                        ->map(function ($dataSet) {
                            return Arr::except($dataSet, 'labels');
                        })
                        ->all(),
                    'labels' => $dataSetsValues->first()['labels'],
                ];
            });

        // Then, panel widgets data
        $data = $data->merge(
            collect($this->panelWidgetsData)
                ->map(function ($value, $key) {
                    return [
                        'key' => $key,
                        'data' => $value,
                    ];
                }),
        );

        // Then, list group widgets data
        $data = $data->merge(
            collect($this->orderedListWidgetsData)
                ->map(function ($items, $key) {
                    $widget = $this->findWidgetByKey($key);

                    $data = collect($items)
                        ->map(function ($item) use ($widget) {
                            return array_merge(
                                $item,
                                ['url' => $widget->getItemUrl($item)],
                            );
                        })
                        ->all();

                    return [
                        'key' => $key,
                        'data' => $data,
                    ];
                }),
        );
        
        // And then, pageAlert
        return $data
            ->when($this->pageAlertData, function (Collection $data, array $pageAlertData) {
                return $data->merge($pageAlertData);
            })
            ->toArray();
    }

    final protected function addGraphDataSet(string $graphWidgetKey, SharpGraphWidgetDataSet $dataSet): self
    {
        $this->graphWidgetDataSets[$graphWidgetKey][] = $dataSet;

        return $this;
    }

    final protected function setPanelData(string $panelWidgetKey, array $data): self
    {
        $this->panelWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    final protected function setOrderedListData(string $panelWidgetKey, array $data): self
    {
        $this->orderedListWidgetsData[$panelWidgetKey] = $data;

        return $this;
    }

    final protected function setPageAlertData(array $data): self
    {
        $this->pageAlertData = [$this->pageAlertHtmlField->key => $data];

        return $this;
    }

    final public function dashboardMetaFields(): array
    {
        if ($this->pageAlertHtmlField) {
            return [
                $this->pageAlertHtmlField->key => $this->pageAlertHtmlField->toArray(),
            ];
        }

        return [];
    }

    private function checkDashboardIsBuilt(): void
    {
        if (! $this->dashboardBuilt) {
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

    abstract protected function buildWidgets(WidgetsContainer $widgetsContainer): void;

    abstract protected function buildDashboardLayout(DashboardLayout $dashboardLayout): void;

    /**
     * Build dashboard's widgets data, using ->addGraphDataSet and ->setPanelData.
     */
    abstract protected function buildWidgetsData(): void;
}
