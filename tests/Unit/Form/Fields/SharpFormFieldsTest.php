<?php

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

it('allows to add a field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('first_name'));
        }
    };

    expect($form->fields())->toHaveCount(2);
});

it('allows to see fields as array', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('first_name'));
        }
    };

    expect($form->fields())
        ->toHaveKey('name.type', 'text')
        ->toHaveKey('first_name.type', 'text');
});
