<?php

namespace Code16\Sharp\Dashboard\Widgets;

trait IsXYChart
{
    protected bool $displayHorizontalAxisAsTimeline = false;
    protected bool $enableHorizontalAxisLabelSampling = false;

    public function setDisplayHorizontalAxisAsTimeline(bool $displayAsTimeline = true): self
    {
        $this->displayHorizontalAxisAsTimeline = $displayAsTimeline;

        return $this;
    }

    public function setEnableHorizontalAxisLabelSampling(bool $enableLabelSampling = true): self
    {
        $this->enableHorizontalAxisLabelSampling = $enableLabelSampling;

        return $this;
    }
}
