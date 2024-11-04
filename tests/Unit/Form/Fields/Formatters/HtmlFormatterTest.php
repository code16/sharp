<?php

use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Illuminate\Support\Str;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new HtmlFormatter())->toFront(SharpFormHtmlField::make('html')->setTemplate('{{ $text }}'), ['text' => $value]))
        ->toEqual($value);
});

it('allows to format value from front', function () {
    expect((new HtmlFormatter())->fromFront(SharpFormHtmlField::make('html'), 'attribute', Str::random()))
        ->toBeNull();
});
