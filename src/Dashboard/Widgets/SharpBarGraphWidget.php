<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpBarGraphWidget extends SharpGraphWidget
{
    use IsXYChart;

    protected bool $horizontal = false;

    public static function make(string $key): SharpBarGraphWidget
    {
        $widget = new static($key, 'graph');
        $widget->display = 'bar';

        return $widget;
    }

    public function setHorizontal(bool $horizontal = true): self
    {
        $this->horizontal = $horizontal;

        return $this;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'displayHorizontalAxisAsTimeline' => $this->displayHorizontalAxisAsTimeline,
            'enableHorizontalAxisLabelSampling' => $this->enableHorizontalAxisLabelSampling,
            'horizontal' => $this->horizontal,
        ];
    }
}
