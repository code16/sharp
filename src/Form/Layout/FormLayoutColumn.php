<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutColumn
{
    /**
     * @var int
     */
    protected $size;

    /**
     * @var array
     */
    protected $fieldsets = [];

    use hasFields;

    /**
     * @param int $size
     */
    function __construct(int $size)
    {
        $this->size = $size;
    }

    /**
     * @param string $name
     * @param \Closure $callback
     * @return $this
     */
    public function withFieldset(string $name, \Closure $callback)
    {
        $fieldset = new FormLayoutFieldset($name);

        $callback($fieldset);

        $this->addFieldsetLayout($fieldset);

        return $this;
    }

    /**
     * @param FormLayoutFieldset $fieldset
     * @return FormLayoutFieldset
     */
    private function addFieldsetLayout(FormLayoutFieldset $fieldset): FormLayoutFieldset
    {
        $this->fieldsets[] = $fieldset;

        return $fieldset;
    }
}