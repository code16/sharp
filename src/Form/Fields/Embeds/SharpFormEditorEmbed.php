<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Illuminate\Support\Str;

abstract class SharpFormEditorEmbed
{
    use HandleFormFields;
    
    protected ?string $label = null;
    protected ?string $tagName = null;
    protected array $templates = [];

    public function toConfigArray(): array
    {
        return [
            'key' => $this->key(),
            'label' => $this->label ?: Str::snake(class_basename(get_class($this))),
            'tag' => $this->tagName ?: 'x-' . Str::snake(class_basename(get_class($this)), '-'),
            'attributes' => collect($this->fields())->keys()->toArray(),
            'previewTemplate' => $this->templates['form'] ?? null,
        ];
    }

    public function buildEmbedConfig(): void
    {
    }

    abstract public function buildFormFields(FieldsContainer $formFields): void;

    protected function configureLabel(string $label): self
    {
        $this->label = $label;
        
        return $this;
    }

    protected function configureTagName(string $tagName): self
    {
        $this->tagName = $tagName;

        return $this;
    }

    protected function configureInlineFormTemplate(string $template): self
    {
        return $this->setTemplate($template, 'form');
    }

    final public function key(): string
    {
        return Str::replace('\\', '.', get_class($this));
    }

    private function setTemplate(string $template, string $key) :self
    {
        $this->templates[$key] = $template;

        return $this;
    }

    protected function configureInlineShowTemplate(string $template): self
    {
        return $this->setTemplate($template, 'show');
    }

    protected function configureInlineFormTemplatePath(string $templatePath): self
    {
        return $this->setTemplate(
            file_get_contents(resource_path('views/'.$templatePath)),
            'form',
        );
    }

    protected function configureInlineShowTemplatePath(string $templatePath): self
    {
        return $this->setTemplate(
            file_get_contents(resource_path('views/'.$templatePath)),
            'show',
        );
    }
}