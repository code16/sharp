<?php

namespace Code16\Sharp\Form\Layout;

use Code16\Sharp\Utils\Fields\HandleFields;

/**
 * @mixin HandleFields
 */
trait HasModalFormLayout
{
    /**
     * @param  (\Closure(FormLayoutColumn):void)  $buildFormLayout
     * @return array|null
     */
    protected function modalFormLayout(\Closure $buildFormLayout): ?array
    {
        if ($fields = $this->fieldsContainer()->getFields()) {
            return (new FormLayout())
                ->setTabbed(false)
                ->addColumn(12, function (FormLayoutColumn $column) use ($fields, $buildFormLayout) {
                    $buildFormLayout($column);

                    if (! $column->hasFields()) {
                        collect($fields)
                            ->each(fn ($field) => $column->withField($field->key()));
                    }
                })
                ->toArray();
        }

        return null;
    }
}
