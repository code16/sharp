<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Dashboard\Widgets\SharpWidget;

abstract class SharpDashboard
{

    /**
     * @var bool
     */
    protected $dashboardBuilt = false;

    /**
     * @var array
     */
    protected $widgets = [];

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

    public function widgets()
    {
        $this->checkDashboardIsBuilt();

        return collect($this->widgets)->map(function($widget) {
            return $widget->toArray();
        })->keyBy("key")->all();
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
}