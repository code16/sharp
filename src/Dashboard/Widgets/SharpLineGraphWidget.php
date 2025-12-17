<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpLineGraphWidget extends SharpGraphWidget
{
    protected bool $curvedLines = true;
    protected bool $displayHorizontalAxisAsTimeline = false;
    protected bool $filled = false;
    protected bool $showAllLabels = false;
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

    public function setFilled(bool $filled = true): self
    {
        $this->filled = $filled;

        return $this;
    }

    public function setDisplayHorizontalAxisAsTimeline(bool $displayAsTimeline = true): self
    {
        $this->displayHorizontalAxisAsTimeline = $displayAsTimeline;

        return $this;
    }

    public function setShowAllLabels(bool $showAllLabels = true): self
    {
        $this->showAllLabels = $showAllLabels;

        return $this;
    }

    public function setShowDots(bool $showDots = true): self
    {
        $this->showDots = $showDots;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(), [
                'dateLabels' => $this->displayHorizontalAxisAsTimeline,
                'showAllLabels' => $this->showAllLabels,
                'options' => [
                    'curved' => $this->curvedLines,
                    'filled' => $this->filled,
                    'showDots' => $this->showDots,
                ],
            ],
        );
    }
}
