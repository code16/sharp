<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpAreaGraphWidget extends SharpGraphWidget
{
    use IsXYChart;

    protected bool $curvedLines = true;
    protected bool $gradient = false;
    protected float $opacity = .4;
    protected bool $stacked = false;
    protected bool $showStackTotal = false;
    protected ?string $stackTotalLabel = null;

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

    public function setStacked(bool $stacked = true): self
    {
        $this->stacked = $stacked;

        return $this;
    }

    public function setShowStackTotal(bool $showTotal = true, ?string $label = null): self
    {
        $this->showStackTotal = $showTotal;
        $this->stackTotalLabel = $label ?? __('sharp::dashboard.widget.graph.total_label');

        return $this;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'displayHorizontalAxisAsTimeline' => $this->displayHorizontalAxisAsTimeline,
            'enableHorizontalAxisLabelSampling' => $this->enableHorizontalAxisLabelSampling,
            'curved' => $this->curvedLines,
            'gradient' => $this->gradient,
            'opacity' => $this->opacity,
            'stacked' => $this->stacked,
            'showStackTotal' => $this->showStackTotal,
            'stackTotalLabel' => $this->stackTotalLabel,
        ];
    }
}
