<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayout;
use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;
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
    protected array $figureWidgetsData = [];
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
            ->map(fn ($widgetContainer) => $widgetContainer->toArray())
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

    final public function data(): array
    {
        $this->buildWidgetsData();

        return collect($this->graphWidgetDataSets)

            // First, graph widgets dataSets
            ->map(function (array $dataSets, string $key) {
                $dataSetsValues = collect($dataSets)
                    ->map(fn ($dataSet) => $dataSet->toArray());

                return [
                    'key' => $key,
                    'datasets' => $dataSetsValues
                        ->map(fn ($dataSet) => Arr::except($dataSet, 'labels'))
                        ->all(),
                    'labels' => $dataSetsValues->first()['labels'],
                ];
            })

            // Then, panel widgets data
            ->merge(
                collect($this->panelWidgetsData)
                    ->map(fn ($data, $key) => [
                        'key' => $key,
                        'data' => $data,
                    ])
            )

            // Then, figure widgets data
            ->merge(
                collect($this->figureWidgetsData)
                    ->map(fn ($data, $key) => [
                        'key' => $key,
                        'data' => $data,
                    ])
            )

            // Then, list group widgets data
            ->merge(
                collect($this->orderedListWidgetsData)
                    ->map(function ($items, $key) {
                        $widget = $this->findWidgetByKey($key);

                        return [
                            'key' => $key,
                            'data' => collect($items)
                                ->map(function ($item) use ($widget) {
                                    return array_merge(
                                        $item,
                                        ['url' => $widget->getItemUrl($item)],
                                    );
                                })
                                ->all(),
                        ];
                    })
            )

            // And then, pageAlert
            ->when(
                $this->pageAlertData,
                fn (Collection $data, array $pageAlertData) => $data->merge($pageAlertData)
            )
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

    final protected function setFigureData(
        string $figureWidgetKey,
        string $figure,
        ?string $unit = null,
        ?string $evolution = null,
    ): self {
        $this->figureWidgetsData[$figureWidgetKey] = [
            'figure' => $figure,
            'unit' => $unit,
            'evolution' => SharpFigureWidget::formatEvolution($evolution),
        ];

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
