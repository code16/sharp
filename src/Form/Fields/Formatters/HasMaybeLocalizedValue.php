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
     * @param  ?Closure<string>  $transformContent
     */
    protected function maybeLocalized(SharpFormField $field, array|string|null $value, ?Closure $transformContent = null): array|string|null
    {
        $transformContent ??= fn ($value) => $value;

        if ($field->isLocalized()) {
            return collect([
                ...collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]),
                ...is_array($value) ? $value : [app()->getLocale() => $value],
            ])
                ->map(fn ($content) => $content !== null ? $transformContent($content) : null)
                ->toArray();
        }

        return $value === null ? null : $transformContent($value);
    }
}
