<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpAreaGraphWidget extends SharpGraphWidget
{
    use IsXYChart;

    protected bool $curvedLines = true;
    protected bool $gradient = false;
    protected float $opacity = .4;

    public static function make(string $key): SharpAreaGraphWidget
    {
        $widget = new static($key, 'graph');
        $widget->display = 'area';

        return $widget;
    }

    public function setCurvedLines(bool $curvedLines = true): self
    {
        $this->curvedLines = $curvedLines;

        return $this;
    }

    public function setOpacity(float $opacity): self
    {
        $this->opacity = $opacity;

        return $this;
    }

    public function setShowGradient(bool $gradient = true): self
    {
        $this->gradient = $gradient;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(), [
                'displayHorizontalAxisAsTimeline' => $this->displayHorizontalAxisAsTimeline,
                'enableHorizontalAxisLabelSampling' => $this->enableHorizontalAxisLabelSampling,
                'options' => [
                    'curved' => $this->curvedLines,
                    'gradient' => $this->gradient,
                    'opacity' => $this->opacity,
                ],
            ],
        );
    }
}
