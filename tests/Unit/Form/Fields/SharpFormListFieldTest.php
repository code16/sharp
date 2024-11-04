<?php

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;

it('sets only default values', function () {
    $formField = buildFakeListField();

    expect($formField->toArray())
        ->toEqual([
            'key' => 'field',
            'type' => 'list',
            'addable' => false,
            'removable' => false,
            'sortable' => false,
            'addText' => 'Add an item',
            'itemIdAttribute' => 'id',
            'bulkUploadLimit' => 10,
            'itemFields' => [
                'text' => [
                    'key' => 'text',
                    'type' => 'text',
                    'inputType' => 'text',
                ],
            ],
        ]);
});

it('allows to define addable removable and sortable', function () {
    $formField = buildFakeListField()
        ->setAddable()
        ->setSortable()
        ->setRemovable();

    expect($formField->toArray())
        ->toHaveKey('addable', true)
        ->toHaveKey('removable', true)
        ->toHaveKey('sortable', true);
});

it('allows to define addText', function () {
    $formField = buildFakeListField()
        ->setAddText('Add');

    expect($formField->toArray())
        ->toHaveKey('addText', 'Add');
});

it('allows to define itemIdAttribute', function () {
    $formField = buildFakeListField()
        ->setItemIdAttribute('key');

    expect($formField->toArray())
        ->toHaveKey('itemIdAttribute', 'key');
});

it('allows to define orderAttribute', function () {
    $formField = buildFakeListField()
        ->setOrderAttribute('ordered');

    $this->assertEquals('ordered', $formField->orderAttribute());
});

it('allows to define maxItemCount', function () {
    $formField = buildFakeListField()
        ->setMaxItemCount(10);

    expect($formField->toArray())
        ->toHaveKey('maxItemCount', 10);

    $formField->setMaxItemCountUnlimited();

    expect($formField->toArray())
        ->not->toHaveKey('maxItemCount');
});

it('allows to define allowBulkUploadForField', function () {
    $formField = buildFakeListField()
        ->allowBulkUploadForField('itemFieldKey');

    expect($formField->toArray())
        ->toHaveKey('bulkUploadField', 'itemFieldKey');

    $formField->doNotAllowBulkUpload();

    expect($formField->toArray())
        ->not->toHaveKey('bulkUploadField');
});

it('allows to define setBulkUploadFileCountLimitAtOnce', function () {
    $formField = buildFakeListField()
        ->setBulkUploadFileCountLimitAtOnce(8);

    expect($formField->toArray())
        ->toHaveKey('bulkUploadLimit', 8);
});

it('allows to add an itemField', function () {
    $formField = buildFakeListField()
        ->addItemField(
            SharpFormTextField::make('name'),
        );

    expect($formField->toArray())
        ->toHaveKey('itemFields', [
            'text' => [
                'key' => 'text',
                'type' => 'text',
                'inputType' => 'text',
            ],
            'name' => [
                'key' => 'name',
                'type' => 'text',
                'inputType' => 'text',
            ],
        ]);
});

it('allows to find an itemField by its key', function () {
    $formField = buildFakeListField()
        ->addItemField(
            SharpFormTextField::make('name'),
        );

    expect($formField->findItemFormFieldByKey('name')->toArray())
        ->toHaveKey('key', 'name');
});

function buildFakeListField(): SharpFormListField
{
    return SharpFormListField::make('field')
        ->addItemField(
            SharpFormTextField::make('text'),
        );
}
