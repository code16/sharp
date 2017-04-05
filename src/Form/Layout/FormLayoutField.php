<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutField
{
    /**
     * @var string
     */
    protected $fieldKey;

    /**
     * @param string $fieldKey
     */
    function __construct(string $fieldKey)
    {
        $this->fieldKey = $fieldKey;
    }
}