<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Utils\Fields\HandleFields;

/**
 * @mixin HandleFields
 */
trait HasModalFormLayout
{
    /**
     * @param  (\Closure(FormLayoutColumn):void)  $buildFormLayout
     */
    protected function modalFormLayout(\Closure $buildFormLayout): ?array
    {
        if ($fields = $this->fieldsContainer()->getFields()) {
            return (new FormLayout())
                ->setTabbed(false)
                ->addColumn(12, function (FormLayoutColumn $column) use ($fields, $buildFormLayout) {
                    $buildFormLayout($column);

                    if (! $column->hasFields()) {
                        // Handle default layout
                        collect($fields)
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
                ->toArray();
        }

        return null;
    }
}
