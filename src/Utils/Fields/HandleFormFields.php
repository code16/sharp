<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;

trait HandleFormFields
{
    use HandleFields;

    /**
     * Applies Field Formatters on $data.
     */
    final public function formatRequestData(array $data, ?string $instanceId = null): array
    {
        return collect($data)
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
    }
}
