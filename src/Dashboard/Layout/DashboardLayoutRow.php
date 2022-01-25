<?php

namespace Code16\Sharp\Dashboard\Layout;

class DashboardLayoutRow
{
    protected array $widgets = [];

    public function addWidget(int $size, string $widgetKey): self
    {
        $this->widgets[] = [
            'size' => $size,
            'key' => $widgetKey,
        ];

        return $this;
    }

    public function toArray(): array
    {
        return $this->widgets;
    }
}
