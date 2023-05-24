<?php

namespace Code16\Sharp\Dashboard\Layout;

class DashboardLayoutSection
{
    protected string $label;
    protected array $rows = [];
    protected ?string $sectionKey = null;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    final public function addRow(\Closure $callback): self
    {
        $row = new DashboardLayoutRow();
        $callback($row);
        $this->rows[] = $row;

        return $this;
    }

    final public function addFullWidthWidget(string $widgetKey): self
    {
        $this->addRow(function (DashboardLayoutRow $row) use ($widgetKey) {
            $row->addWidget(12, $widgetKey);
        });

        return $this;
    }

    final public function setKey(string $key): self
    {
        $this->sectionKey = $key;

        return $this;
    }

    final public function addRowLayout(DashboardLayoutRow $row): DashboardLayoutRow
    {
        $this->rows[] = $row;

        return $row;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->sectionKey,
            'label' => $this->label,
            'rows' => collect($this->rows)
                ->map(fn (DashboardLayoutRow $row) => $row->toArray())
                ->toArray(),
        ];
    }
}
