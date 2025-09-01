<?php

use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Illuminate\Support\Str;

it('allows to format value to front, passthrough value', function () {
    expect(
        (new HtmlFormatter())->toFront(
            SharpFormHtmlField::make('html')->setTemplate('<b>{{ $text }}</b>'),
            ['text' => 'example']
        )
    )
        ->toEqual(['text' => 'example']);
});

it('allows to format value with view to front, passthrough value', function () {
    expect(
        (new HtmlFormatter())->toFront(
            SharpFormHtmlField::make('html')->setTemplate(view('fixtures::test')),
            ['text' => 'example']
        )
    )
        ->toEqual(['text' => 'example']);
});

it('allows to format value from front', function () {
    expect((new HtmlFormatter())->fromFront(SharpFormHtmlField::make('html'), 'attribute', Str::random()))
        ->toBeNull();
});
