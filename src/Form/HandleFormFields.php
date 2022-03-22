<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;

trait HandleFormFields
{
    protected array $fields = [];
    protected bool $formBuilt = false;

    /**
     * Get the SharpFormField array representation.
     */
    public function fields(): array
    {
        $this->checkFormIsBuilt();

        return collect($this->fields)
            ->map->toArray()
            ->keyBy('key')
            ->all();
    }

    /**
     * Return the key attribute of all fields defined in the form.
     */
    public function getDataKeys(): array
    {
        return collect($this->fields())
            ->pluck('key')
            ->all();
    }

    /**
     * Find a field by its key.
     *
     * @param  string  $key
     * @return SharpFormField|SharpShowField
     */
    public function findFieldByKey(string $key)
    {
        $this->checkFormIsBuilt();

        $fields = collect($this->fields);

        if (strpos($key, '.') !== false) {
            [$key, $itemKey] = explode('.', $key);
            $listField = $fields->where('key', $key)->first();

            return $listField->findItemFormFieldByKey($itemKey);
        }

        return $fields->where('key', $key)->first();
    }

    /**
     * Add a field.
     *
     * @param  SharpFormField|SharpShowField  $field
     * @return $this
     */
    protected function addField($field): self
    {
        $this->fields[] = $field;
        $this->formBuilt = false;

        return $this;
    }

    /**
     * Applies Field Formatters on $data.
     *
     * @param  array  $data
     * @param  string|null  $instanceId
     * @param  bool  $handleDelayedData
     * @return array
     */
    public function formatRequestData(array $data, $instanceId = null, bool $handleDelayedData = false): array
    {
        $delayedData = collect([]);

        $formattedData = collect($data)
            ->filter(function ($value, $key) {
                // Filter only configured fields
                return in_array($key, $this->getDataKeys());
            })

            ->map(function ($value, $key) use ($handleDelayedData, $delayedData, $instanceId) {
                if (! $field = $this->findFieldByKey($key)) {
                    return $value;
                }

                try {
                    // Apply formatter based on field configuration
                    return $field
                        ->formatter()
                        ->setInstanceId($instanceId)
                        ->fromFront($field, $key, $value);
                } catch (SharpFormFieldFormattingMustBeDelayedException $exception) {
                    // The formatter needs to be executed in a second pass. We delay it.
                    if ($handleDelayedData) {
                        $delayedData[$key] = $value;

                        return null;
                    }

                    throw $exception;
                }
            });

        if ($handleDelayedData) {
            return [
                $formattedData
                    ->filter(function ($value, $key) use ($delayedData) {
                        return ! $delayedData->has($key);
                    })
                    ->all(),
                $delayedData->all(),
            ];
        }

        return $formattedData->all();
    }

    /**
     * Check if the form was previously built, and build it if not.
     */
    private function checkFormIsBuilt(): void
    {
        if (! $this->formBuilt) {
            $this->buildFormFields();
            $this->formBuilt = true;
        }
    }
}
