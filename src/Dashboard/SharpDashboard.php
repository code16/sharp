<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\Widgets\SharpWidget;

abstract class SharpDashboard
{

    /**
     * @var bool
     */
    protected $dashboardBuilt = false;

    /**
     * @var bool
     */
    protected $layoutBuilt = false;

    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * Add a widget.
     *
     * @param SharpWidget $widget
     * @return $this
     */
    protected function addWidget(SharpWidget $widget)
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
    protected function addFullWidthWidget(string $widgetKey)
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
    protected function addRow(\Closure $callback)
    {
        $row = new DashboardLayoutRow();

        $callback($row);

        $this->rows[] = $row;

        return $this;
    }

    public function widgets()
    {
        $this->checkDashboardIsBuilt();

        return collect($this->widgets)->map(function(SharpWidget $widget) {
            return $widget->toArray();
        })->keyBy("key")->all();
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
            "rows" => collect($this->rows)->map(function(DashboardLayoutRow $row) {
                return $row->toArray();
            })->all()
        ];
    }

    /**
     * Return data, as an array.
     *
     * @param array|Collection|null $items
     * @return array
     */
    function data($items = null): array
    {
        $items = $items ?: $this->getWidgetsData();

        return [
            "widgets" => $items
        ];
    }

    private function checkDashboardIsBuilt()
    {
        if (!$this->dashboardBuilt) {
            $this->buildWidgets();
            $this->dashboardBuilt = true;
        }
    }

    /**
     * Build dashboard's widget using ->addWidget.
     */
    protected abstract function buildWidgets();

    /**
     * Build dashboard's widgets layout.
     */
    protected abstract function buildWidgetsLayout();

    /**
     * Return dashboard's widgets data as an array.
     *
     * @return array
     */
    protected abstract function getWidgetsData();
}