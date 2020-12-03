<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpBarGraphWidget extends SharpGraphWidget
{
    /** @var bool */
    protected $horizontal = false;

    /** @var bool */
    protected $displayHorizontalAxisAsTimeline = false;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
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

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(), [
                "dateLabels" => $this->displayHorizontalAxisAsTimeline,
                "options" => [
                    "horizontal" => $this->horizontal
                ]
            ]
        );
    }
}