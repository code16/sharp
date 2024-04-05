<?php

namespace Code16\Sharp\Utils\Fields\Formatters;

use Closure;
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
     * @param  IsSharpFieldWithLocalization  $field
     * @param  ?Closure<string>  $transformContent
     */
    protected function maybeLocalized(IsSharpFieldWithLocalization $field, array|string|null $value, ?Closure $transformContent = null): array|string|null
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
