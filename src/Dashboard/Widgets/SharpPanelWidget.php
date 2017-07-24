<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Code16\Sharp\Form\Fields\SharpFormHtmlField;

class SharpPanelWidget extends SharpWidget
{

    /**
     * @var SharpFormHtmlField
     */
    protected $htmlFormField;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'panel');
        $widget->htmlFormField = SharpFormHtmlField::make('panel');

        return $widget;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "template" => $this->htmlFormField->template()
        ]);
    }

    /**
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath(string $templatePath)
    {
        $this->htmlFormField->setTemplatePath($templatePath);

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setInlineTemplate(string $template)
    {
        $this->htmlFormField->setInlineTemplate($template);

        return $this;
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
            "template" => "required"
        ];
    }

}