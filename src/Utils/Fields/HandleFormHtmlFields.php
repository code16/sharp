<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;

trait HandleFormHtmlFields
{
    final public function hasHtmlFields(): bool
    {
        return collect($this->getBuiltFields())
            ->whereInstanceOf(SharpFormHtmlField::class)
            ->merge(
                collect($this->getBuiltFields())
                    ->whereInstanceOf(SharpFormListField::class)
                    ->map(fn ($field) => $field->itemFields()->whereInstanceOf(SharpFormHtmlField::class))
            )
            ->flatten()
            ->isNotEmpty();
    }

    final public function formatHtmlFields(array $frontData, bool $keepOnlyRefreshableFields = false): array
    {
        if (! $this->hasHtmlFields()) {
            return $frontData;
        }

        $formattedData = $this->formatRequestData($frontData);

        return collect($frontData)
            ->mapWithKeys(function ($value, $key) use ($formattedData, $keepOnlyRefreshableFields) {
                $field = $this->findFieldByKey($key);

                if ($field instanceof SharpFormHtmlField) {
                    return $keepOnlyRefreshableFields && ! $field->hasLiveRefresh()
                        ? []
                        : [
                            $key => $field->render([
                                ...$formattedData,
                                ...(is_array($value) ? $value : []),
                            ]),
                        ];
                }

                if ($field instanceof SharpFormListField
                    && $field->itemFields()->whereInstanceOf(SharpFormHtmlField::class)->isNotEmpty()
                ) {
                    return [
                        $key => $this->formatListHtmlFields($field, $value, $formattedData, $keepOnlyRefreshableFields),
                    ];
                }

                return $keepOnlyRefreshableFields ? [] : [$key => $value];
            })
            ->all();
    }

    private function formatListHtmlFields(
        SharpFormListField $field,
        array $listValue,
        array $formattedData,
        bool $keepOnlyRefreshableFields
    ): array {
        return collect($listValue)->map(fn ($item, $index) => collect($item)->mapWithKeys(
            function ($itemFieldValue, $itemFieldKey) use ($field, $index, $formattedData, $keepOnlyRefreshableFields) {
                $itemField = $field->findItemFormFieldByKey($itemFieldKey);

                if ($itemField instanceof SharpFormHtmlField) {
                    return $keepOnlyRefreshableFields && ! $itemField->hasLiveRefresh()
                        ? []
                        : [
                            $itemFieldKey => $itemField->render([
                                ...($formattedData[$field->key][$index] ?? []),
                                ...(is_array($itemFieldValue) ? $itemFieldValue : []),
                            ]),
                        ];
                }

                return $keepOnlyRefreshableFields ? [] : [$itemFieldKey => $itemFieldValue];
            })->all()
        )->all();
    }
}
