<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;

trait BuildsSharpFormLayout
{
    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * Return the form fields layout.
     *
     * @return array
     */
    function formLayout(): array
    {
        return collect($this->tabs)->map(function($tab) {
            return $tab->toArray();
        })->all();
    }

    /**
     * @param string $label
     * @return FormLayoutTab
     */
    function addTab(string $label): FormLayoutTab
    {
        return $this->addTabLayout(new FormLayoutTab($label));
    }

    /**
     * @param int $size
     * @return FormLayoutColumn
     */
    function addColumn(int $size): FormLayoutColumn
    {
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

}