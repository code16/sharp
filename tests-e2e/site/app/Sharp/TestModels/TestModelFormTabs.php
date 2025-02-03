<?php

namespace App\Sharp\TestModels;

use Code16\Sharp\Form\Layout\FormLayoutTab;

class TestModelFormTabs extends TestModelForm
{
    public function buildFormLayout($formLayout): void
    {
        $formLayout
            ->addTab('Tab 1', function (FormLayoutTab $tab) {
                $this->buildTestFieldsLayout($tab);
            })
            ->addTab('Tab 2', function (FormLayoutTab $tab) {});
    }
}
