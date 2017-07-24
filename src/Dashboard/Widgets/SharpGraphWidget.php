<?php

namespace Code16\Sharp\Dashboard\Widgets;

abstract class SharpGraphWidget extends SharpWidget
{

    /**
     * @var string
     */
    protected $display;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "display" => $this->display
        ]);
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
            "display" => "required|in:bar,line,pie"
        ];
    }

}