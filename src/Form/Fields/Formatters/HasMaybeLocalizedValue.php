<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Closure;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithDataLocalization;

/**
 * @mixin SharpFieldFormatter
 */
trait HasMaybeLocalizedValue
{
    /**
     * @param  SharpFormField&SharpFormFieldWithDataLocalization  $field
     * @param  Closure<string>  $callback
     */
    protected function maybeLocalized(SharpFormField $field, array|string|null $value, Closure $callback): array|string|null
    {
        if ($value === null) {
            return null;
        }

        if ($field->isLocalized()) {
            return collect([
                ...collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]),
                ...is_array($value) ? $value : [app()->getLocale() => $value],
            ])
                ->map(fn ($content) => $content !== null ? $callback($content) : null)
                ->toArray();
        }

        return $callback($value);
    }
}
