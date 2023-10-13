<?php

use Code16\Sharp\Dashboard\Widgets\SharpWidget;
use Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException;
use Code16\Sharp\Utils\Links\LinkToEntityList;

it('must define a key', function () {
    $this->expectException(SharpWidgetValidationException::class);
    buildTestWidget()->toArray();
});

it('must define a type', function () {
    $this->expectException(SharpWidgetValidationException::class);
    buildTestWidget('type', '')->toArray();
});

it('returns an array which contains key and type', function () {
    $widget = buildTestWidget('name', 'test');

    expect($widget->toArray())->toHaveKey('key', 'name')
        ->toHaveKey('type', 'test');
});

it('allows to define title', function () {
    $widget = buildTestWidget('name')
        ->setTitle('my title');

    expect($widget->toArray())->toHaveKey('title', 'my title');
});

it('allows to define a SharpLinkTo link', function () {
    $widget = buildTestWidget('name')
        ->setLink(LinkToEntityList::make('entity'));

    expect($widget->toArray()['link'])->toEqual(route('code16.sharp.list', 'entity'));
});

function buildTestWidget(?string $key = '', ?string $type = 'type'): SharpWidget
{
    return new class($key, $type) extends SharpWidget
    {
        public function __construct($key, $type)
        {
            parent::__construct($key, $type);
        }

        public function toArray(): array
        {
            return parent::buildArray([]);
        }
    };
}
