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

    const FIELD_TYPE = "html";

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, static::FIELD_TYPE, new HtmlFormatter);
    }

    /**
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath(string $templatePath)
    {
        return $this->parentSetTemplatePath($templatePath, "html");
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setInlineTemplate(string $template)
    {
        return $this->parentSetInlineTemplate($template, "html");
    }

    /**
     * @return string
     */
    public function template()
    {
        return $this->parentTemplate("html");
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "template" => "required",
        ];
    }

    /**
     * @return array
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "template" => $this->template(),
        ]);
    }
}