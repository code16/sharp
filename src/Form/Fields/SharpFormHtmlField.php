<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithTemplates;

class SharpFormHtmlField extends SharpFormField
{
    use SharpFormFieldWithTemplates {
        setTemplatePath as protected parentSetTemplatePath;
        setInlineTemplate as protected parentSetInlineTemplate;
        template as protected parentTemplate;
    }

    const FIELD_TYPE = 'html';

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, new HtmlFormatter);
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
            'template' => 'required',
            'templateData' => 'nullable|array',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'template' => $this->template(),
            'templateData' => $this->additionalTemplateData,
        ]);
    }
}
