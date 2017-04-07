<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;

abstract class SharpForm
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * @var bool
     */
    protected $formBuilt = false;

    /**
     * @var bool
     */
    protected $layoutBuilt = false;

    /**
     * Get the SharpFormField array representation.
     *
     * @return array
     */
    function fields(): array
    {
        if(!$this->formBuilt) {
            $this->buildFormFields();
            $this->formBuilt = true;
        }

        return collect($this->fields)->map(function($field) {
            return $field->toArray();
        })->all();
    }

    /**
     * Return the form fields layout.
     *
     * @return array
     */
    function formLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildFormLayout();
            $this->layoutBuilt = true;
        }

        return collect($this->tabs)->map(function($tab) {
            return $tab->toArray();
        })->all();
    }

    /**
     * Add a field.
     *
     * @param SharpFormField $field
     */
    protected function addField(SharpFormField $field)
    {
        $this->fields[] = $field;
        $this->formBuilt = false;
    }

    /**
     * @param string $label
     * @return FormLayoutTab
     */
    protected function addTab(string $label): FormLayoutTab
    {
        $this->layoutBuilt = false;

        return $this->addTabLayout(new FormLayoutTab($label));
    }

    /**
     * @param int $size
     * @return FormLayoutColumn
     */
    protected function addColumn(int $size): FormLayoutColumn
    {
        $this->layoutBuilt = false;
        $tab = $this->addTab("one");

        return $tab->addColumn($size);
    }

    /**
     * @param FormLayoutTab $tab
     * @return FormLayoutTab
     */
    private function addTabLayout(FormLayoutTab $tab): FormLayoutTab
    {
        $this->tabs[] = $tab;

        return $tab;
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     *
     * @param $id
     * @return array
     */
    abstract function get($id): array;

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    abstract function update($id, array $data): bool;

    /**
     * @param array $data
     * @return bool
     */
    abstract function store(array $data): bool;

    /**
     * @param $id
     * @return bool
     */
    abstract function delete($id): bool;

    /**
     * Build form fields using ->addField()
     *
     * @return void
     */
    abstract function buildFormFields();

    /**
     * Build form layout using ->addTab() or ->addColumn()
     *
     * @return void
     */
    abstract function buildFormLayout();
}