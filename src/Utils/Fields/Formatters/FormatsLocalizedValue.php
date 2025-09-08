<?php

namespace Code16\Sharp\Utils\Fields\Formatters;

use Closure;
use Code16\Sharp\Exceptions\Form\SharpFormFieldDataException;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;

trait FormatsLocalizedValue
{
    protected ?array $dataLocalizations = null;

    public function setDataLocalizations(array $dataLocalizations): static
    {
        $this->dataLocalizations = $dataLocalizations;

        return $this;
    }

    /**
     * @throws SharpFormFieldDataException
     * @throws SharpInvalidConfigException
     */
    protected function guardAgainstInvalidLocalizedValue(SharpFormField|SharpShowField $field, $value): void
    {
        if (! $field instanceof IsSharpFieldWithLocalization) {
            return;
        }

        if (! $field->isLocalized() && is_array($value)) {
            throw new SharpFormFieldDataException(sprintf(
                'String expected, got an Array for field value "%s". If the field is localized, add `‑>setLocalized()`',
                $field->key()
            ));
        }

        if ($field->isLocalized() && is_string($value)) {
            throw new SharpFormFieldDataException(sprintf(
                'Array expected, got a String for field value "%s". The field has `‑>setLocalized()` so its value must be an array like ["en"=>"text"] or null',
                $field->key()
            ));
        }

        if ($field->isLocalized() && ! count($this->dataLocalizations ?? [])) {
            throw new SharpInvalidConfigException(sprintf(
                'The "%s" field is localized but no locales are defined. Use the `getDataLocalizations()` method to define them.',
                $field->key()
            ));
        }
    }

    /**
     * @param  ?Closure<string, ?string>  $transformContent
     */
    protected function maybeLocalized(SharpFormField|SharpShowField $field, $value, ?Closure $transformContent = null): array|string|null
    {
        $transformContent ??= fn ($value) => $value;

        if ($field instanceof IsSharpFieldWithLocalization && $field->isLocalized() && ! is_string($value)) {
            return collect($this->dataLocalizations ?? [])
                ->mapWithKeys(fn ($locale) => [$locale => null])
                ->merge($value)
                ->map(fn ($content, $locale) => $content !== null ? $transformContent($content, $locale) : null)
                ->toArray();
        }

        return $value === null ? null : $transformContent($value, null);
    }
}
