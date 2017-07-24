<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithTemplates
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @param string $templatePath
     * @param string $key
     * @return $this
     */
    protected function setTemplatePath(string $templatePath, string $key)
    {
        return $this->setInlineTemplate(
            file_get_contents(resource_path("views/" . $templatePath)),
            $key
        );
    }

    /**
     * @param string $template
     * @param string $key
     * @return $this
     */
    protected function setInlineTemplate(string $template, string $key)
    {
        $this->templates[$key] = $template;

        return $this;
    }

    /**
     * @param string $key
     * @return string|null
     */
    protected function template(string $key)
    {
        return $this->templates[$key] ?? null;
    }

}