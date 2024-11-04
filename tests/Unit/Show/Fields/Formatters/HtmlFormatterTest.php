<?php


use Code16\Sharp\Show\Fields\Formatters\HtmlFormatter;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Illuminate\Support\Str;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new HtmlFormatter())->toFront(SharpShowHtmlField::make('html')->setTemplate('{{ $text }}'), ['text' => $value]))
        ->toEqual($value);
});
