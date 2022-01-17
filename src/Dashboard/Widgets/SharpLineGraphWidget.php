<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpLineGraphWidget extends SharpGraphWidget
{
    protected bool $curvedLines = true;
    protected bool $displayHorizontalAxisAsTimeline = false;

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

    public function setDisplayHorizontalAxisAsTimeline(bool $displayAsTimeline = true): self
    {
        $this->displayHorizontalAxisAsTimeline = $displayAsTimeline;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'dateLabels' => $this->displayHorizontalAxisAsTimeline,
                'options'    => [
                    'curved' => $this->curvedLines,
                ],
            ]
        );
    }
}
