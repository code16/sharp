<?php

namespace Code16\Sharp\Dashboard\Layout;

use Code16\Sharp\Form\Layout\HasLayout;

class DashboardLayout implements HasLayout
{
    protected array $rows = [];

    public final function addFullWidthWidget(string $widgetKey): self
    {
        $this->addRow(function (DashboardLayoutRow $row) use ($widgetKey) {
            $row->addWidget(12, $widgetKey);
        });

        return $this;
    }

    public final function addRow(\Closure $callback): self
    {
        $row = new DashboardLayoutRow();

        $callback($row);

        $this->rows[] = $row;

        return $this;
    }

    public function toArray(): array
    {
        return [
            "rows" => collect($this->rows)
                ->map->toArray()
                ->toArray()
        ];
    }
}