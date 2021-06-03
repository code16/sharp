<?php

namespace Code16\Sharp\Form\Fields\Utils;

trait SharpFormFieldWithTemplates
{
    protected array $templates = [];
    protected array $additionalTemplateData = [];

    protected function setTemplatePath(string $templatePath, string $key): self
    {
        return $this->setInlineTemplate(
            file_get_contents(resource_path("views/" . $templatePath)),
            $key
        );
    }

    protected function setInlineTemplate(string $template, string $key): self
    {
        $this->templates[$key] = $template;

        return $this;
    }

    protected function template(string $key): ?string
    {
        return $this->templates[$key] ?? null;
    }

    protected function setTemplateData(array $data): self
    {
        $this->additionalTemplateData = $data;

        return $this;
    }
}