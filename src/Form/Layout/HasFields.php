<?php

namespace Code16\Sharp\Form\Layout;

trait HasFields
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param string $fieldKey
     * @return $this
     */
    public function withField(string $fieldKey)
    {
        $this->addFieldLayout(new FormLayoutField($fieldKey));

        return $this;
    }

    /**
     * @param \string[] ...$fieldKeys
     * @return $this
     */
    public function withFields(string ...$fieldKeys)
    {
        foreach($fieldKeys as $fieldKey) {
            $this->addFieldLayout(new FormLayoutField($fieldKey));
        }

        return $this;
    }

    /**
     * @param FormLayoutField $field
     * @return FormLayoutField
     */
    private function addFieldLayout(FormLayoutField $field): FormLayoutField
    {
        $this->fields[] = $field;

        return $field;
    }
}