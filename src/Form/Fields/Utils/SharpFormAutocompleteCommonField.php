<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;

trait SharpFormAutocompleteCommonField
{
    use SharpFormFieldWithPlaceholder;
    use SharpFormFieldWithOptions;
    use SharpFieldWithLocalization;

    const FIELD_TYPE = 'autocomplete';

    protected string $mode;
    protected string $itemIdAttribute = 'id';
    protected ?array $dynamicAttributes = null;
    protected View|string|null $listItemTemplate = null;
    protected View|string|null $resultItemTemplate = null;
    
    public function itemWithRenderedTemplates(array $item): array
    {
        return [
            ...$item,
            '_html' => $this->listItemTemplate
                ? $this->renderListItem($item)
                : ($item['label'] ?? $item[$this->itemIdAttribute] ?? null),
            '_htmlResult' => $this->resultItemTemplate ? $this->renderResultItem($item) : null,
        ];
    }

    public function setItemIdAttribute(string $itemIdAttribute): self
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }
    
    public function setListItemTemplate(View|string $template): self
    {
        $this->listItemTemplate = $template;
        
        return $this;
    }
    
    public function setResultItemTemplate(View|string $template): self
    {
        $this->resultItemTemplate = $template;
        
        return $this;
    }
    
    public function renderListItem(array $data): string
    {
        if (is_string($this->listItemTemplate)) {
            return Blade::render($this->listItemTemplate, $data);
        }
        
        return $this->listItemTemplate->with($data)->render();
    }
    
    public function renderResultItem(array $data): string
    {
        if (is_string($this->resultItemTemplate)) {
            return Blade::render($this->resultItemTemplate, $data);
        }
        
        return $this->resultItemTemplate->with($data)->render();
    }

    public function setAdditionalTemplateData(array $data): self
    {
        // TODO keep this ?
        
        return $this;
    }

    public function isRemote(): bool
    {
        return $this->mode === 'remote';
    }

    public function isLocal(): bool
    {
        return $this->mode === 'local';
    }

    public function itemIdAttribute(): string
    {
        return $this->itemIdAttribute;
    }

    protected function validationRulesBase(): array
    {
        return [
            'mode' => 'required|in:local,remote',
            'itemIdAttribute' => 'required',
//            'listItemTemplate' => 'required',
//            'resultItemTemplate' => 'required',
            'templateData' => 'nullable|array',
        ];
    }

    protected function toArrayBase(): array
    {
        return array_merge(
            [
                'mode' => $this->mode,
                'placeholder' => $this->placeholder,
                'itemIdAttribute' => $this->itemIdAttribute,
                'listItemTemplate' => $this->listItemTemplate,
                'resultItemTemplate' => $this->resultItemTemplate,
                'localized' => $this->localized,
            ],
            $this->dynamicAttributes
                ? ['dynamicAttributes' => $this->dynamicAttributes]
                : [],
        );
    }
}
