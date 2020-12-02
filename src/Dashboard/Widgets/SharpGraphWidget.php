<?php

namespace Code16\Sharp\Dashboard\Widgets;

abstract class SharpGraphWidget extends SharpWidget
{
    /** @var string */
    protected $display = null;

    /** @var string */
    protected $ratio = [16,9];

    /** @var int */
    protected $height = null;

    /** @var bool */
    protected $showLegend = true;

    /** @var bool */
    protected $minimal = false;

    /**
     * @param string $ratio 16:9, 1:1, ...
     * @return self
     */
    public function setRatio(string $ratio): self
    {
        $this->ratio = explode(":", $ratio);

        return $this;
    }

    /**
     * @param int $height an arbitrary height (ratio will be ignored)
     * @return self
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function setShowLegend(bool $showLegend = true): self
    {
        $this->showLegend = $showLegend;

        return $this;
    }

    public function setMinimal(bool $minimal = true): self
    {
        $this->minimal = $minimal;

        return $this;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "display" => $this->display,
            "ratioX" => $this->ratio ? (int)$this->ratio[0] : null,
            "ratioY" => $this->ratio ? (int)$this->ratio[1] : null,
            "height" => $this->height,
            "minimal" => $this->minimal,
            "showLegend" => $this->showLegend,
        ]);
    }

    protected function validationRules(): array
    {
        return [
            "display" => "required|in:bar,line,pie"
        ];
    }
}