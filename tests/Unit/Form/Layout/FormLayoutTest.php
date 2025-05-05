<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldLayoutException;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

it('allows to add a tab', function () {
    $form = new class() extends FakeSharpForm
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
    $form = new class() extends FakeSharpForm
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
    $form = new class() extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addTab('label');
        }
    };

    expect($form->formLayout()['tabs'][0])
        ->toEqual(['title' => 'label', 'columns' => []]);

    $form2 = new class() extends FakeSharpForm
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
    $form = new class() extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addTab('label')->setTabbed(false);
        }
    };

    expect($form->formLayout()['tabbed'])
        ->toBeFalse();
});

it('allows to add a field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function ($column) {
                $column->withField('name');
            });
        }
    };

    expect($form->formLayout()['tabs'][0]['columns'][0]['fields'][0][0])
        ->key->toBe('name')
        ->size->toBe(12);
});

it('allows to add a list field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormListField::make('jobs')
                    ->addItemField(SharpFormTextField::make('title'))
            );
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function ($column) {
                $column->withListField('jobs', function ($field) {
                    $field->withField('title');
                });
            });
        }
    };

    $formLayout = $form->formLayout();

    expect($formLayout['tabs'][0]['columns'][0]['fields'][0][0])
        ->key->toBe('jobs')
        ->and($formLayout['tabs'][0]['columns'][0]['fields'][0][0]['item'][0][0])
        ->key->toBe('title');
});

it('fails when adding a undeclared field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function ($column) {
                $column->withField('name');
            });
        }
    };
    $form->formLayout();
})->throws(SharpFormFieldLayoutException::class, SharpFormFieldLayoutException::undeclaredField('name')->getMessage());

it('fails when adding a list field as field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormListField::make('jobs')
                        ->addItemField(SharpFormTextField::make('title'))
                );
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function ($column) {
                $column->withField('jobs');
            });
        }
    };
    $form->formLayout();
})->throws(SharpFormFieldLayoutException::class, SharpFormFieldLayoutException::listFieldDeclaredAsRegularField('jobs')->getMessage());

it('fails when adding a field as list field', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('title'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(12, function ($column) {
                $column->withListField('name', function ($field) {
                    $field->withField('title');
                });
            });
        }
    };
    $form->formLayout();
})->throws(SharpFormFieldLayoutException::class, SharpFormFieldLayoutException::regularFieldDeclaredAsListField('name')->getMessage());
