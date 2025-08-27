<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;

trait HandleFormHtmlFields
{
    final public function hasHtmlFields(bool $withLiveRefresh = false): bool
    {
        $htmlFields = collect($this->getBuiltFields())
            ->whereInstanceOf(SharpFormHtmlField::class)
            ->merge(
                collect($this->getBuiltFields())
                    ->whereInstanceOf(SharpFormListField::class)
                    ->map(fn ($field) => $field->itemFields()->whereInstanceOf(SharpFormHtmlField::class))
            )
            ->flatten();

        return $withLiveRefresh ? $htmlFields->some->hasLiveRefresh() : $htmlFields->isNotEmpty();
    }

    final public function formatHtmlFields(array $frontData, bool $keepOnlyHtmlFields = false): array
    {
        if (! $this->hasHtmlFields()) {
            return $frontData;
        }

        $formattedData = $this->formatRequestData($frontData);

        return collect($frontData)
            ->mapWithKeys(function ($value, $key) use ($formattedData, $keepOnlyHtmlFields) {
                $field = $this->findFieldByKey($key);

                if ($field instanceof SharpFormHtmlField) {
                    return [
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
                        $key => $this->formatListHtmlFields($field, $value, $formattedData, $keepOnlyHtmlFields),
                    ];
                }

                return $keepOnlyHtmlFields ? [] : [$key => $value];
            })
            ->all();
    }

    private function formatListHtmlFields(
        SharpFormListField $field,
        array $listValue,
        array $formattedData,
        bool $keepOnlyHtmlFields
    ): array {
        return collect($listValue)->map(fn ($item, $index) => collect($item)->mapWithKeys(
            function ($itemFieldValue, $itemFieldKey) use ($field, $index, $formattedData, $keepOnlyHtmlFields) {
                $itemField = $field->findItemFormFieldByKey($itemFieldKey);

                if ($itemField instanceof SharpFormHtmlField) {
                    return [
                        $itemFieldKey => $itemField->render([
                            ...($formattedData[$field->key][$index] ?? []),
                            ...(is_array($itemFieldValue) ? $itemFieldValue : []),
                        ]),
                    ];
                }

                return $keepOnlyHtmlFields ? [] : [$itemFieldKey => $itemFieldValue];
            })->all()
        )->all();
    }
}
