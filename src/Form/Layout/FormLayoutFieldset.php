<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutFieldset implements HasLayout
{
    /** @var string */
    protected $legend;

    use HasFieldRows;

    /**
     * @param string $legend
     */
    function __construct(string $legend)
    {
        $this->legend = $legend;
    }

    /**
     * @return array
     */
    function toArray(): array
    {
        return [
            "legend" => $this->legend
        ] + $this->fieldsToArray();
    }
}