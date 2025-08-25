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
                            'fieldKey' => $key,
                            ...$formattedData,
                            ...(is_array($value) ? $value : []),
                        ], $key),
                    ];
                }

                if ($field instanceof SharpFormListField && $field->itemFields()->whereInstanceOf(SharpFormHtmlField::class)->isNotEmpty()) {
                    return collect($value)->map(function ($item, $index) use ($field, $key, $formattedData, $keepOnlyHtmlFields) {
                        return collect($item)->mapWithKeys(function ($itemValue, $itemKey) use ($field, $key, $index, $formattedData, $keepOnlyHtmlFields) {
                            $itemField = $field->findItemFormFieldByKey($itemKey);

                            if ($itemField instanceof SharpFormHtmlField) {
                                $fieldKey = "$key.$index.$itemKey";

                                return [
                                    $itemKey => $itemField->render([
                                        'fieldKey' => $fieldKey,
                                        ...$formattedData,
                                        ...(is_array($itemValue) ? $itemValue : []),
                                    ], $fieldKey),
                                ];
                            }

                            return $keepOnlyHtmlFields ? [] : [$key => $itemValue];
                        })->all();
                    })->all();
                }

                return $keepOnlyHtmlFields ? [] : [$key => $value];
            })
            ->all();
    }
}
