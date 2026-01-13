<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpLineGraphWidget extends SharpGraphWidget
{
    use IsXYChart;

    protected bool $curvedLines = true;
    protected bool $showDots = false;

    public static function make(string $key): SharpLineGraphWidget
    {
        $widget = new static($key, 'graph');
        $widget->display = 'line';

        return $widget;
    }

    public function setCurvedLines(bool $curvedLines = true): self
    {
        $this->curvedLines = $curvedLines;

        return $this;
    }

    public function setShowDots(bool $showDots = true): self
    {
        $this->showDots = $showDots;

        return $this;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'displayHorizontalAxisAsTimeline' => $this->displayHorizontalAxisAsTimeline,
            'enableHorizontalAxisLabelSampling' => $this->enableHorizontalAxisLabelSampling,
            'curved' => $this->curvedLines,
            'showDots' => $this->showDots,
        ];
    }
}
