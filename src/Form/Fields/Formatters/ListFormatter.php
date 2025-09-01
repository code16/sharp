<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Closure;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;

class ListFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    protected ?Closure $formatItemFieldUsing = null;

    /**
     * @param  Closure<SharpFieldFormatter>  $formatItemFieldUsing
     */
    public function formatItemFieldUsing(Closure $formatItemFieldUsing): static
    {
        $this->formatItemFieldUsing = $formatItemFieldUsing;

        return $this;
    }

    public function toFront(SharpFormField $field, $value)
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                $itemArray = [
                    $field->itemIdAttribute() => $item[$field->itemIdAttribute()],
                ];

                $field
                    ->itemFields()
                    ->each(function ($itemField) use ($item, &$itemArray) {
                        $key = $itemField->key();

                        if (str_contains($key, ':')) {
                            // It's a sub attribute (like mother:name)
                            [$attribute, $subAttribute] = explode(':', $key);
                            $itemArray[$key] = isset($item[$attribute][$subAttribute])
                                ? $itemField
                                    ->formatter()
                                    ->setDataLocalizations($this->dataLocalizations ?: [])
                                    ->toFront($itemField, $item[$attribute][$subAttribute])
                                : null;
                        } else {
                            $itemArray[$key] = isset($item[$key])
                                ? $itemField
                                    ->formatter()
                                    ->setDataLocalizations($this->dataLocalizations ?: [])
                                    ->toFront($itemField, $item[$key])
                                : null;
                        }
                    });

                return $itemArray;
            })
            ->all();
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                $itemArray = [
                    $field->itemIdAttribute() => $item[$field->itemIdAttribute()],
                ];

                foreach ($item as $key => $value) {
                    $itemField = $field->findItemFormFieldByKey($key);

                    if ($itemField && ! $itemField instanceof SharpFormHtmlField) {
                        $itemArray[$key] = $this->itemFieldFormatter($itemField)
                            ->setInstanceId($this->instanceId)
                            ->fromFront($itemField, $key, $value);
                    }
                }

                return $itemArray;
            })
            ->all();
    }

    /**
     * @param  SharpFormListField  $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): array
    {
        return collect($value)
            ->map(function ($item) use ($field) {
                foreach ($item as $key => $value) {
                    $itemField = $field->findItemFormFieldByKey($key);

                    if ($itemField && $itemField->formatter() instanceof FormatsAfterUpdate) {
                        $item[$key] = $itemField->formatter()
                            ->setInstanceId($this->instanceId)
                            ->afterUpdate($itemField, $key, $value);
                    }
                }

                return $item;
            })
            ->all();
    }

    protected function itemFieldFormatter(SharpFormField $field): SharpFieldFormatter
    {
        if ($callback = $this->formatItemFieldUsing) {
            return $callback($field);
        }

        return $field->formatter();
    }
}
