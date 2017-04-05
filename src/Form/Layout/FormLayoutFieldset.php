<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutFieldset
{
    /**
     * @var string
     */
    protected $legend;

    use hasFields;

    /**
     * @param string $legend
     */
    function __construct(string $legend)
    {
        $this->legend = $legend;
    }
}