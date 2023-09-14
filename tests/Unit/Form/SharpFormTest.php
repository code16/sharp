<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpForm;
use Code16\Sharp\Tests\Unit\Form\Fakes\FakeSharpSingleForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

it('returns form fields', function () {
    $form = new class extends FakeSharpForm
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
    $form = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormTextField::make('age'));
        }

        public function buildFormLayout(FormLayout $formLayout): void
        {
            $formLayout->addColumn(6, fn ($column) => $column->withSingleField('name'))
                ->addColumn(6, fn ($column) => $column->withSingleField('age'));
        }
    };

    expect($form->formLayout())
        ->toEqual([
            'tabbed' => true,
            'tabs' => [[
                'title' => 'one',
                'columns' => [[
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'name',
                            'size' => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ], [
                    'size' => 6,
                    'fields' => [[
                        [
                            'key' => 'age',
                            'size' => 12,
                            'sizeXS' => 12,
                        ],
                    ]],
                ]],
            ]],
        ]);
});

it('gets an instance', function () {
    $form = new class extends FakeSharpForm {
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

it('formats data in creation with the default create function', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormEditorField::make('md'))
                ->addField(SharpFormCheckField::make('check', 'text'));
        }
    };

    $this->assertEquals(
        [
            'md' => ['text' => null],
            'check' => false,
        ],
        $sharpForm->newInstance(),
    );
});

it('formats data in creation with the default create function with subclasses', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(SharpFormTextField::make('name'))
                ->addField(SharpFormEditorField::make('subclass:company'));
        }
    };

    $this->assertEquals(
        [
            'name' => '',
            'subclass:company' => ['text' => null],
        ],
        $sharpForm->newInstance(),
    );
});

it('delays execution after first save if the field formatter needs to', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public $instance;

        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields
                ->addField(
                    SharpFormTextField::make('normal'),
                )
                ->addField(
                    SharpFormTextField::make('delayed')
                        ->setFormatter(new class extends SharpFieldFormatter
                        {
                            public function toFront(SharpFormField $field, $value)
                            {
                            }

                            public function fromFront(SharpFormField $field, string $attribute, $value)
                            {
                                if (! $this->instanceId) {
                                    throw new SharpFormFieldFormattingMustBeDelayedException();
                                }

                                return $value.'-'.$this->instanceId;
                            }
                        }),
                );
        }

        public function update($id, array $data)
        {
            if (! $id) {
                $this->instance = ['id' => 1] + $data;
            } else {
                $this->instance += $data;
            }

            return 1;
        }
    };

    $sharpForm->storeInstance([
        'normal' => 'abc',
        'delayed' => 'abc',
    ]);

    $this->assertEquals(
        [
            'id' => 1,
            'normal' => 'abc',
            'delayed' => 'abc-1',
        ],
        $sharpForm->instance,
    );
});

it('raises an exception if we try to delay but the update does not return the instance id', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('delayed')
                    ->setFormatter(new class extends SharpFieldFormatter
                    {
                        public function toFront(SharpFormField $field, $value)
                        {
                        }

                        public function fromFront(SharpFormField $field, string $attribute, $value)
                        {
                            throw new SharpFormFieldFormattingMustBeDelayedException();
                        }
                    }),
            );
        }
    };

    $this->expectException(SharpFormUpdateException::class);
    $sharpForm->storeInstance([
        'delayed' => 'abc',
    ]);
});

it('handles single forms', function () {
    $sharpForm = new FakeSharpSingleForm();

    $sharpForm->buildFormConfig();

    $this->assertEquals(
        [
            'isSingle' => true,
            'hasShowPage' => false,
            'deleteConfirmationText' => null,
        ],
        $sharpForm->formConfig(),
    );
});

it('allows to declare setDisplayShowPageAfterCreation in config', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildFormConfig(): void
        {
            $this->configureDisplayShowPageAfterCreation();
        }
    };

    $sharpForm->buildFormConfig();

    $this->assertEquals(
        [
            'hasShowPage' => true,
            'deleteConfirmationText' => null,
        ],
        $sharpForm->formConfig(),
    );
});

it('allows to declare a page alert', function () {
    $sharpForm = new class extends FakeSharpForm
    {
        public function buildPageAlert(PageAlert $pageAlert): void
        {
            $pageAlert
                ->setLevelInfo()
                ->setMessage('My page alert');
        }
    };

    expect($sharpForm->pageAlert())
        ->toEqual([
            'text' => 'My page alert',
            'level' => \Code16\Sharp\Enums\PageAlertLevel::Info->value,
        ]);
});