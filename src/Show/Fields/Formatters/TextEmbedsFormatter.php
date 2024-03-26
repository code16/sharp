<?php

namespace Code16\Sharp\Show\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\HandlesHtmlContent;
use Code16\Sharp\Form\Fields\Formatters\HasMaybeLocalizedValue;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsEditorEmbedsToFront;

class TextEmbedsFormatter extends SharpShowFieldFormatter
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;
    use FormatsEditorEmbedsToFront;

    /**
     * @param  SharpShowTextField  $field
     */
    public function toFront(SharpShowField $field, $value)
    {
        return $this->formatsEditorEmbedsToFront($field, $value);
    }
}
