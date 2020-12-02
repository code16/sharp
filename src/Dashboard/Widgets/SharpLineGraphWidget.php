<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpLineGraphWidget extends SharpGraphWidget
{
    /** @var bool */
    protected $curvedLines = true;

    /** @var bool */
    protected $displayHorizontalAxisAsTimeline = false;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
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
            parent::toArray(), [
                "dateValues" => $this->displayHorizontalAxisAsTimeline,
                "options" => [
                    "curved" => $this->curvedLines
                ]
            ]
        );
    }
}