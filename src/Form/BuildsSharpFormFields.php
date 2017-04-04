<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormField;

trait BuildsSharpFormFields
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var bool
     */
    protected $formBuilt = false;

    /**
     * Get the fields array representation.
     *
     * @return array
     */
    function fields(): array
    {
        if(!$this->formBuilt) {
            $this->buildForm();
            $this->formBuilt = true;
        }

        return collect($this->fields)->map(function($field) {
            return $field->toArray();
        })->all();
    }

    /**
     * Build the form, using `addField()`.
     */
    function buildForm(): void {}

    /**
     * Add a field.
     *
     * @param SharpFormField $field
     */
    function addField(SharpFormField $field)
    {
        $this->fields[] = $field;
    }
}