<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\Formatters\FormatsAfterUpdate;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;

trait HandleFormFields
{
    use HandleFields;

    /**
     * Applies Field Formatters and validate $data.
     */
    final public function formatAndValidateRequestData(array $data, ?string $instanceId = null): array
    {
        $legacyValidation = property_exists($this, 'formValidatorClass');

        if ($legacyValidation) {
            // Legacy support (v8 and below): first validate, then format
            app($this->formValidatorClass);
        }

        $formattedData = collect($data)
            ->filter(function ($value, $key) {
                // Ignore HTML fields
                if ($this->findFieldByKey($key) instanceof SharpFormHtmlField) {
                    return false;
                }

                // Filter only configured fields
                return in_array($key, $this->getDataKeys());
            })
            ->map(function ($value, $key) use ($instanceId) {
                if (! $field = $this->findFieldByKey($key)) {
                    return $value;
                }

                return $field
                    ->formatter()
                    ->setInstanceId($instanceId)
                    ->setDataLocalizations($this->getDataLocalizations())
                    ->fromFront($field, $key, $value);
            })
            ->all();

        if (! $legacyValidation) {
            if (method_exists($this, 'rules')) {
                $this->validate(
                    $formattedData,
                    $this->rules($formattedData),
                    method_exists($this, 'messages') ? $this->messages($formattedData) : []
                );
            }
        }

        return $formattedData;
    }

    final public function formatDataAfterUpdate(array $data, string $instanceId): array
    {
        return collect($data)
            ->map(function ($value, $key) use ($instanceId) {
                if (! $field = $this->findFieldByKey($key)) {
                    return $value;
                }
                if ($field->formatter() instanceof FormatsAfterUpdate) {
                    return $field
                        ->formatter()
                        ->setInstanceId($instanceId)
                        ->afterUpdate($field, $key, $value);
                }

                return $value;
            })
            ->all();
    }
}
