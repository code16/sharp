<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithTemplates;

class SharpShowHtmlField extends SharpShowField
{
    use SharpFormFieldWithTemplates {
        setTemplatePath as protected parentSetTemplatePath;
        setInlineTemplate as protected parentSetInlineTemplate;
        template as protected parentTemplate;
    }

    const FIELD_TYPE = 'html';

    protected ?string $label = null;

    public static function make(string $key): SharpShowHtmlField
    {
        return new static($key, static::FIELD_TYPE);
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setTemplatePath(string $templatePath): self
    {
        return $this->parentSetTemplatePath($templatePath, 'html');
    }

    public function setInlineTemplate(string $template): self
    {
        return $this->parentSetInlineTemplate($template, 'html');
    }

    public function setAdditionalTemplateData(array $data): self
    {
        return $this->setTemplateData($data);
    }

    public function template(): ?string
    {
        return $this->parentTemplate('html');
    }

    protected function validationRules(): array
    {
        return [
            'template'     => 'required',
            'templateData' => 'nullable|array',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'template'     => $this->template(),
            'templateData' => $this->additionalTemplateData,
        ]);
    }
}
