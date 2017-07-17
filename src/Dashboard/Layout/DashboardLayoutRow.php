<?php

namespace Code16\Sharp\Dashboard\Layout;

class DashboardLayoutRow
{

    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * @param int $size
     * @param string $widgetKey
     * @return $this
     */
    public function addWidget(int $size, string $widgetKey)
    {
        $this->widgets[] = [
            "size" => $size,
            "key" => $widgetKey
        ];

        return $this;
    }

    public function toArray()
    {
        return $this->widgets;
    }
}