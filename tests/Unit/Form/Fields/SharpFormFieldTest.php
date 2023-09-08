<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormField;

it('forces to define a key', function () {
    $this->expectException(SharpFormFieldValidationException::class);
    buildFakeFormField('')->toArray();
});

it('forces to define a type', function () {
    $this->expectException(SharpFormFieldValidationException::class);
    buildFakeFormField('name', '')->toArray();
});

it('returns key and type in the array', function () {
    $formField = buildFakeFormField('name', 'test');

    expect($formField->toArray())
        ->toHaveKey('key', 'name')
        ->toHaveKey('type', 'test');
});

it('does not return null attributes in the array', function () {
    $formField = buildFakeFormField('name');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'name',
            'type' => 'test',
        ]);
});

it('allows to define label', function () {
    $formField = buildFakeFormField('name')
        ->setLabel('my label');

    expect($formField->toArray())
        ->toHaveKey('label', 'my label');
});

it('allows to define helpMessage', function () {
    $formField = buildFakeFormField('name')
        ->setHelpMessage('message');

    expect($formField->toArray())
        ->toHaveKey('helpMessage', 'message');
});

it('allows to define conditionalDisplay', function () {
    $formField = buildFakeFormField('name')
        ->setConditionalDisplayOrOperator()
        ->addConditionalDisplay('is_displayed')
        ->addConditionalDisplay('color', ['blue', 'red'])
        ->addConditionalDisplay('size', '!xl')
        ->addConditionalDisplay('!hidden')
        ->addConditionalDisplay('really_hidden', false);

    expect($formField->toArray())
        ->toHaveKey('conditionalDisplay.operator', 'or')
        ->toHaveKey('conditionalDisplay.fields', [
            [
                'key' => 'is_displayed',
                'values' => true,
            ], [
                'key' => 'color',
                'values' => ['blue', 'red'],
            ], [
                'key' => 'size',
                'values' => '!xl',
            ], [
                'key' => 'hidden',
                'values' => false,
            ], [
                'key' => 'really_hidden',
                'values' => false,
            ],
        ]);
});

it('allows to define readOnly', function () {
    $formField = buildFakeFormField('name')
        ->setReadOnly();

    expect($formField->toArray())
        ->toHaveKey('readOnly', true);
});

it('allows to define_extraStyle', function () {
    $formField = buildFakeFormField('name')
        ->setExtraStyle('font-weight: bold');

    expect($formField->toArray())
        ->toHaveKey('extraStyle', 'font-weight: bold');
});

function buildFakeFormField(string $key, string $type = 'test'): SharpFormField
{
    return new class($key, $type) extends SharpFormField
    {
        public function __construct(string $key, string $type)
        {
            parent::__construct($key, $type);
        }

        public function toArray(): array
        {
            return parent::buildArray([]);
        }
    };
}
