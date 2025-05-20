<?php

namespace Code16\Sharp\Form\Layout;

use Closure;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Utils\Fields\HandleFields;

/**
 * @mixin HandleFields
 */
trait HasModalFormLayout
{
    /**
     * @param  (Closure(FormLayoutColumn):void)  $buildFormLayout
     */
    protected function modalFormLayout(Closure $buildFormLayout): ?array
    {
        if (($fields = collect($this->fieldsContainer()->getFields()))->isNotEmpty()) {
            return (new FormLayout())
                ->setTabbed(false)
                ->addColumn(12, function (FormLayoutColumn $column) use ($fields, $buildFormLayout) {
                    $buildFormLayout($column);

                    if (! $column->hasFields()) {
                        // Handle default layout
                        $fields
                            ->each(fn (SharpFormField $field) => $field instanceof SharpFormListField
                                ? $column->withListField($field->key, fn ($layout) => $field
                                    ->itemFields()
                                    ->each(fn (SharpFormField $itemField) => $layout
                                        ->withSingleField($itemField->key())
                                    )
                                )
                                : $column->withField($field->key)
                            );
                    }
                })
                ->validateAgainstFields($fields->keyBy('key'))
                ->toArray();
        }

        return null;
    }
}
