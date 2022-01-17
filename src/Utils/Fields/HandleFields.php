<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Illuminate\Support\Collection;

trait HandleFields
{
    protected ?FieldsContainer $fieldsContainer = null;
    protected bool $formBuilt = false;

    final public function fieldsContainer(): FieldsContainer
    {
        if ($this->fieldsContainer === null) {
            $this->fieldsContainer = new FieldsContainer();
        }

        return $this->fieldsContainer;
    }

    /**
     * Get the SharpFormField|SharpShowField array representation.
     */
    final public function fields(): array
    {
        $this->checkFormIsBuilt();

        return collect($this->fieldsContainer()->getFields())
            ->when($this->pageAlertHtmlField, function (Collection $collection) {
                return $collection->push($this->pageAlertHtmlField);
            })
            ->map->toArray()
            ->keyBy('key')
            ->all();
    }

    /**
     * Return the key attribute of all fields defined in the form.
     */
    final public function getDataKeys(): array
    {
        return collect($this->fields())
            ->pluck('key')
            ->all();
    }

    final public function findFieldByKey(string $key): SharpFormField|SharpShowField|null
    {
        $this->checkFormIsBuilt();

        $fields = collect($this->fieldsContainer()->getFields());

        if (str_contains($key, '.')) {
            list($key, $itemKey) = explode('.', $key);
            $listField = $fields->where('key', $key)->first();

            return $listField->findItemFormFieldByKey($itemKey);
        }

        return $fields->where('key', $key)->first();
    }

    /**
     * Check if the form was previously built, and build it if not.
     */
    private function checkFormIsBuilt(): void
    {
        if (!$this->formBuilt) {
            $this->buildFormFields($this->fieldsContainer());
            $this->formBuilt = true;
        }
    }
}
