<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\EditorUploadsFormatter;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Illuminate\Support\Collection;

class TextFieldFormatter extends SharpShowFieldFormatter
{
    /**
     * @param SharpShowTextField $field
     */
    public function toFront(SharpShowField $field, $value)
    {
        return collect(['text' => $value])
            ->pipeThrough([
                fn (Collection $collection) => $collection->merge(
                    $this->editorUploadsFormatter()->toFront($field, $collection['text'])
                ),
//                fn (Collection $collection) => $collection->merge(
//                    $this->editorEmbedsFormatter()->toFront($field, $collection['text'])
//                ),
            ])
            ->toArray();
    }

    protected function editorUploadsFormatter(): TextUploadsFormatter
    {
        return (new TextUploadsFormatter())
            ->setDataLocalizations($this->dataLocalizations ?? []);
    }

    protected function editorEmbedsFormatter(): EditorEmbedsFormatter
    {
        return (new EditorEmbedsFormatter())
            ->setDataLocalizations($this->dataLocalizations ?? [])
            ->setInstanceId($this->instanceId);
    }
}
