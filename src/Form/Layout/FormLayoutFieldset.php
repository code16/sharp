<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutFieldset implements HasLayout
{
    use HasFieldRows;

    protected string $legend;

    public function __construct(string $legend)
    {
        $this->legend = $legend;
    }
    
    public function setLegend(string $legend)
    {
        $this->legend = $legend;
    }

    public function toArray(): array
    {
        return array_merge(
            ['legend' => $this->legend],
            $this->fieldsToArray(),
        );
    }
}
