<?php

namespace App\Sharp\Posts\Blocks;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;

class PostBlockTextForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'text';
    protected ?string $formValidatorClass = PostBlockTextValidator::class;

    protected function getContentField(): ?SharpFormField
    {
        return SharpFormEditorField::make('content')
            ->setLabel('Content')
            ->setToolbar([
                SharpFormEditorField::B,
                SharpFormEditorField::I,
                SharpFormEditorField::A,
            ])
            ->setWithoutParagraphs()
            ->setHeight(200);
    }
}
