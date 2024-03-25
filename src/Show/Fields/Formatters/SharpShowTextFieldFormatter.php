<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Show\Fields\SharpShowField;
use Illuminate\Support\Collection;

class SharpShowTextFieldFormatter extends SharpShowFieldFormatter
{
    public function toFront(SharpShowField $field, $value)
    {
        return collect(['text' => $value])
            ->pipeThrough([
                fn (Collection $collection) => $collection->merge(
                    $this->editorUploadsFormatter()->toFront($field, $collection['text'])
                ),
                fn (Collection $collection) => $collection->merge(
                    $this->editorEmbedsFormatter()->toFront($field, $collection['text'])
                ),
            ])
            ->toArray();
    }
}