<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutColumn implements HasLayout
{
    use HasFieldRows;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var array
     */
    protected $fieldsets = [];

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
     */
    private function addFieldsetLayout(FormLayoutFieldset $fieldset)
    {
        $this->rows[] = [$fieldset];
    }

    /**
     * @return array
     */
    function toArray(): array
    {
        return [
            "size" => $this->size
        ] + $this->fieldsToArray();
    }
}