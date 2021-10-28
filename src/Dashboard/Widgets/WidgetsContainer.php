<?php

namespace Code16\Sharp\Dashboard\Widgets;

class WidgetsContainer
{
    protected array $widgets = [];

    public final function addWidget(SharpWidget $widget): self
    {
        $this->widgets[] = $widget;

        return $this;
    }

    public function getWidgets(): array
    {
        return $this->widgets;
    }
}