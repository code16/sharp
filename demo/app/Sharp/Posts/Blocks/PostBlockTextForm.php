<?php

namespace App\Sharp\Posts\Blocks;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;

class PostBlockTextForm extends AbstractPostBlockForm
{
    protected static string $postBlockType = 'text';

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

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
}
