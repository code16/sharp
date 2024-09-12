<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Code16\Sharp\Utils\Fields\SharpFieldWithLocalization;

trait SharpFormAutocompleteCommonField
{
    use SharpFormFieldWithPlaceholder;
    use SharpFormFieldWithTemplates;
    use SharpFormFieldWithOptions;
    use SharpFieldWithLocalization;

    const FIELD_TYPE = 'autocomplete';

    protected string $mode;
    protected string $itemIdAttribute = 'id';
    protected ?array $dynamicAttributes = null;

    public function setItemIdAttribute(string $itemIdAttribute): self
    {
        $this->itemIdAttribute = $itemIdAttribute;

        return $this;
    }

    public function setListItemTemplatePath(string $listItemTemplatePath): self
    {
        $this->setTemplatePath($listItemTemplatePath, 'list');

        return $this;
    }

    public function setResultItemTemplatePath(string $resultItemTemplate): self
    {
        $this->setTemplatePath($resultItemTemplate, 'result');

        return $this;
    }

    public function setListItemInlineTemplate(string $template): self
    {
        return $this->setInlineTemplate($template, 'list');
    }

    public function setResultItemInlineTemplate(string $template): self
    {
        return $this->setInlineTemplate($template, 'result');
    }

    public function setAdditionalTemplateData(array $data): self
    {
        return $this->setTemplateData($data);
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
            'listItemTemplate' => 'required',
            'resultItemTemplate' => 'required',
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
                'templateData' => $this->additionalTemplateData,
                'listItemTemplate' => $this->template('list'),
                'resultItemTemplate' => $this->template('result'),
                'localized' => $this->localized,
            ],
            $this->dynamicAttributes
                ? ['dynamicAttributes' => $this->dynamicAttributes]
                : [],
        );
    }
}