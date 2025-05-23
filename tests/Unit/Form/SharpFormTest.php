<?php

use Code16\Sharp\Enums\PageAlertLevel;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

it('returns form fields', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'));
        }
    };

    expect($form->fields())
        ->toEqual([
            'name' => [
                'key' => 'name',
                'type' => 'text',
                'inputType' => 'text',
            ],
        ]);
});

it('returns form layout', function () {
    $form = new class() extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(6, fn ($column) => $column->withField('name'))
                ->addColumn(6, fn ($column) => $column->withField('age'));
        }
    };

    expect($form->formLayout())
        ->toEqual([
            'tabbed' => true,
            'tabs' => [[
                'title' => '',
                'columns' => [[
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'name',
                            'size' => 12,
                        ],
                    ]],
                ], [
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'age',
                            'size' => 12,
                        ],
                    ]],
                ]],
            ]],
        ]);
});

it('gets an instance', function () {
    $form = new class() extends FakeSharpForm
    {
        public function find($id): array
        {
            return [
                'name' => 'Marie Curie',
                'age' => 22,
                'job' => 'actor',
            ];
        }

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }
    };

    expect($form->instance(1))
        ->toEqual([
            'name' => 'Marie Curie',
            'age' => 22,
        ]);
});

it('handles single forms', function () {
    $sharpForm = new FakeSharpSingleForm();

    $sharpForm->buildFormConfig();

    $this->assertEquals(
        [
            'isSingle' => true,
        ],
        $sharpForm->formConfig(),
    );
});

it('allows to declare a page alert', function () {
    $sharpForm = new class() extends FakeSharpForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert')
                ->setButton('My button', 'https://example.com');
        }
    };

    expect($sharpForm->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => PageAlertLevel::Info,
            'buttonLabel' => 'My button',
            'buttonUrl' => 'https://example.com',
        ]);
});
