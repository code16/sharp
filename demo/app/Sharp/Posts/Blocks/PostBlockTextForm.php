<?php

namespace App\Sharp\Posts\Blocks;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class PostBlockTextForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'text';
    protected ?string $formValidatorClass = PostBlockTextValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('type')
                    ->setInlineTemplate('Post block type: <strong>{{name}}</strong><div class="small" v-if="help">{{help}}</div>'),
            )
            ->addField(
                SharpFormEditorField::make('content')
                    ->setLabel('Content')
                    ->setToolbar([
                        SharpFormEditorField::B,
                        SharpFormEditorField::I,
                        SharpFormEditorField::A,
                    ])
                    ->setWithoutParagraphs()
                    ->setHeight(200),
            );
    }
}
