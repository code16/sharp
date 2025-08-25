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
     * @return Collection<string, SharpFormField>
     */
    final public function getBuiltFields(): Collection
    {
        $this->checkFormIsBuilt();

        return collect($this->fieldsContainer()->getFields())
            ->mapWithKeys(fn (SharpFormField|SharpShowField $field) => [
                $field->key => $field,
            ]);
    }

    /**
     * Get the SharpFormField|SharpShowField array representation.
     */
    final public function fields(): array
    {
        return $this->getBuiltFields()
            ->when($this->pageTitleField ?? null, fn ($collection) => $collection->push($this->pageTitleField))
            ->map(fn ($collection) => $collection->toArray())
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
        $fields = $this->getBuiltFields();

        if (str_contains($key, '.')) {
            [$key, $itemKey] = explode('.', $key);

            return $fields[$key]->findItemFormFieldByKey($itemKey);
        }

        return $fields[$key] ?? null;
    }

    final public function applyFormatters(?array $attributes): ?array
    {
        if (! $attributes) {
            return null;
        }

        return collect($this->getDataKeys())
            ->mapWithKeys(fn ($dataKey) => [$dataKey => null])
            ->merge($attributes)
            ->map(function ($value, $key) {
                if (isset($this->pageTitleField) && $this->pageTitleField->key == $key) {
                    return $this->pageTitleField
                        ->formatter()
                        ->setDataLocalizations($this->getDataLocalizations())
                        ->toFront($this->pageTitleField, $value);
                }

                $field = $this->findFieldByKey($key);

                return $field
                    ? $field->formatter()
                        ->setDataLocalizations($this->getDataLocalizations())
                        ->toFront($field, $value)
                    : $value;
            })
            ->when(
                in_array(HandleFormHtmlFields::class, class_uses_recursive(static::class)),
                fn ($data) => collect($this->formatHtmlFields($data->all()))
            )
            ->all();
    }

    /**
     * Check if the form was previously built, and build it if not.
     */
    protected function checkFormIsBuilt(): void
    {
        if (! $this->formBuilt) {
            $this->buildFormFields($this->fieldsContainer());
            $this->formBuilt = true;
        }
    }
}
