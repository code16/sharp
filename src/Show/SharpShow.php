<?php

namespace Code16\Sharp\Show;

use Code16\Sharp\Form\HandleFormFields;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpShow
{
    use WithCustomTransformers, HandleFormFields;

    public function showLayout()
    {
        return [];
    }

    /**
     * Return the entity instance, as an array.
     *
     * @param $id
     * @return array
     */
    function instance($id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual show labels
            ->only($this->getDataKeys())
            ->all();
    }

    function buildFormFields()
    {
        $this->buildShowFields();
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    abstract function find($id): array;

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    abstract function buildShowFields();

    /**
     * Build form layout using ->addSection()
     *
     * @return void
     */
    abstract function buildShowLayout();
}