<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;

trait HandleFormFields
{
    use HandleFields;
    
    /**
     * Applies Field Formatters on $data.
     */
    public final function formatRequestData(array $data, ?string $instanceId = null, bool $handleDelayedData = false): array
    {
        $delayedData = collect();

        $formattedData = collect($data)
            ->filter(function ($value, $key) {
                // Filter only configured fields
                return in_array($key, $this->getDataKeys());
            })

            ->map(function($value, $key) use($handleDelayedData, $delayedData, $instanceId) {
                if(!$field = $this->findFieldByKey($key)) {
                    return $value;
                }

                try {
                    // Apply formatter based on field configuration
                    return $field
                        ->formatter()
                        ->setInstanceId($instanceId)
                        ->fromFront($field, $key, $value);

                } catch(SharpFormFieldFormattingMustBeDelayedException $exception) {
                    // The formatter needs to be executed in a second pass. We delay it.
                    if($handleDelayedData) {
                        $delayedData[$key] = $value;
                        return null;
                    }

                    throw $exception;
                }
            });

        if($handleDelayedData) {
            return [
                $formattedData
                    ->filter(function ($value, $key) use ($delayedData) {
                        return !$delayedData->has($key);
                    })
                    ->all(),
                $delayedData->all()
            ];
        }

        return $formattedData->all();
    }
}