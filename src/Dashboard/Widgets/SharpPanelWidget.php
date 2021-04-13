<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;

class SharpPanelWidget extends SharpWidget
{
    protected SharpFormHtmlField $htmlFormField;

    public static function make(string $key): self
    {
        $widget = new static($key, 'panel');
        $widget->htmlFormField = SharpFormHtmlField::make('panel');

        return $widget;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            "template" => $this->htmlFormField->template()
        ]);
    }

    public function setTemplatePath(string $templatePath): self
    {
        $this->htmlFormField->setTemplatePath($templatePath);

        return $this;
    }

    public function setInlineTemplate(string $template): self
    {
        $this->htmlFormField->setInlineTemplate($template);

        return $this;
    }

    protected function validationRules(): array
    {
        return [
            "template" => "required"
        ];
    }
}