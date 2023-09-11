<?php

use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;

it('allows to add a tab', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addTab('label');
        }
    };

    expect($form->formLayout()['tabs'])
        ->toHaveCount(1);
});

it('allows to add a column', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(2);
        }
    };

    expect($form->formLayout()['tabs'][0]['columns'])
        ->toHaveCount(1);
});

it('allows to see layout as array', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addTab('label');
        }
    };

    expect($form->formLayout()['tabs'][0])
        ->toEqual(['title' => 'label', 'columns' => []]);

    $form2 = new class extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(2);
        }
    };

    expect($form2->formLayout()['tabs'][0]['columns'][0]['size'])
        ->toEqual(2);
});

it('allows to set tabbed to false', function () {
    $form = new class extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addTab('label')->setTabbed(false);
        }
    };

    expect($form->formLayout()['tabbed'])
        ->toBeFalse();
});
