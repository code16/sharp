<?php

namespace Code16\Sharp\Tests\Http\Api\Fixtures;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

trait RefreshFormFields
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
            )
            ->addField(
                SharpFormHtmlField::make('html_non_refreshable')
                    ->setTemplate('<h1>{{ $name }}</h1><p>{{ $text }}</p>')
            )
            ->addField(
                SharpFormHtmlField::make('html_string')
                    ->setLiveRefresh()
                    ->setTemplate('<h1>{{ $name }}</h1><p>{{ $text }}</p>')
            )
            ->addField(
                SharpFormHtmlField::make('html_view')
                    ->setLiveRefresh()
                    ->setTemplate(view('fixtures::form-html-field'))
            )
            ->addField(
                SharpFormHtmlField::make('html_closure')
                    ->setLiveRefresh()
                    ->setTemplate(fn ($data) => sprintf('<h1>%s</h1><p>%s</p>', $data['name'], $data['text'])
                    )
            )
            ->addField(
                SharpFormListField::make('list')
                    ->addItemField(
                        SharpFormTextField::make('list_name')
                    )
                    ->addItemField(
                        SharpFormHtmlField::make('list_html')
                            ->setLiveRefresh()
                            ->setTemplate(fn ($data) => sprintf('<h1>%s</h1><p>%s</p>', $data['list_name'], $data['text'])
                            )
                    )
            );
    }
}
