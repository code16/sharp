<?php

namespace Code16\Sharp\Dashboard\Layout;

use Code16\Sharp\Form\Layout\HasLayout;

class DashboardLayout implements HasLayout
{
    protected array $sections = [];

    final public function addSection(string $label, \Closure $callback): self
    {
        $section = new DashboardLayoutSection($label);
        $callback($section);
        $this->sections[] = $section;

        return $this;
    }

    final public function addRow(\Closure $callback): self
    {
        $row = $this
            ->getLonelySection()
            ->addRowLayout(new DashboardLayoutRow());

        $callback($row);

        return $this;
    }

    final public function addFullWidthWidget(string $widgetKey): self
    {
        $this->getLonelySection()->addFullWidthWidget($widgetKey);

        return $this;
    }

    private function getLonelySection(): DashboardLayoutSection
    {
        if (! sizeof($this->sections)) {
            return $this->addSectionLayout(new DashboardLayoutSection('one'));
        }

        return $this->sections[0];
    }
    
    private function addSectionLayout(DashboardLayoutSection $section): DashboardLayoutSection
    {
        $this->sections[] = $section;

        return $section;
    }

    public function toArray(): array
    {
        return [
            'sections' => collect($this->sections)
                ->map(fn (DashboardLayoutSection $section) => $section->toArray())
                ->toArray(),
        ];
    }
}
