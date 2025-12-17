<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpBarGraphWidget extends SharpGraphWidget
{
    protected bool $horizontal = false;
    protected bool $displayHorizontalAxisAsTimeline = false;
    protected bool $showAllLabels = false;

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

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(), [
                'dateLabels' => $this->displayHorizontalAxisAsTimeline,
                'showAllLabels' => $this->showAllLabels,
                'options' => [
                    'horizontal' => $this->horizontal,
                ],
            ],
        );
    }
}
