<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormField;

trait HandleFormFields
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $formBuilt = false;

    /**
     * Get the SharpFormField array representation.
     *
     * @return array
     */
    function fields(): array
    {
        $this->checkFormIsBuilt();

        return collect($this->fields)->map(function($field) {
            return $field->toArray();
        })->keyBy("key")->all();
    }

    /**
     * Return the key attribute of all fields defined in the form.
     *
     * @return array
     */
    function getDataKeys(): array
    {
        return collect($this->fields())
            ->pluck("key")
            ->all();
    }

    /**
     * Find a field by its key.
     *
     * @param string $key
     * @return SharpFormField
     */
    function findFieldByKey(string $key)
    {
        $this->checkFormIsBuilt();

        $fields = collect($this->fields);

        if(strpos($key, ".") !== false) {
            list($key, $itemKey) = explode(".", $key);
            $listField = $fields->where("key", $key)->first();

            return $listField->findItemFormFieldByKey($itemKey);
        }

        return $fields->where("key", $key)->first();
    }

    /**
     * Add a field.
     *
     * @param SharpFormField $field
     * @return $this
     */
    protected function addField(SharpFormField $field)
    {
        $this->fields[] = $field;
        $this->formBuilt = false;

        return $this;
    }

    /**
     * Applies Field Formatters on $data.
     *
     * @param array $data
     * @return array
     */
    public function formatRequestData(array $data): array
    {
        return collect($data)->filter(function ($value, $key) {
            // Filter only configured fields
            return in_array($key, $this->getDataKeys());

        })->map(function($value, $key) {
            $field = $this->findFieldByKey($key);

            // Apply formatter based on field configuration
            return $field
                ? $field->formatter()->fromFront($field, $key, $value)
                : $value;

        })->all();
    }

    private function checkFormIsBuilt()
    {
        if (!$this->formBuilt) {
            $this->buildFormFields();
            $this->formBuilt = true;
        }
    }
}