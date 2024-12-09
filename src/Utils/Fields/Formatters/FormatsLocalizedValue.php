<?php

namespace Code16\Sharp\Utils\Fields\Formatters;

use Closure;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use Illuminate\Contracts\Support\Arrayable;

trait FormatsLocalizedValue
{
    protected ?array $dataLocalizations = null;

    public function setDataLocalizations(array $dataLocalizations): static
    {
        $this->dataLocalizations = $dataLocalizations;

        return $this;
    }

    /**
     * @param  ?Closure<string>  $transformContent
     */
    protected function maybeLocalized(SharpFormField|SharpShowField $field, array|Arrayable|string|null $value, ?Closure $transformContent = null): array|string|null
    {
        $transformContent ??= fn ($value) => $value;

        if ($field instanceof IsSharpFieldWithLocalization && $field->isLocalized()) {
            $value = $value instanceof Arrayable ? $value->toArray() : $value;
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
