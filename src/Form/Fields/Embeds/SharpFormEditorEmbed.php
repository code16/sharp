<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Str;

abstract class SharpFormEditorEmbed
{
    use HandleFormFields,
        HandlePageAlertMessage,
        WithCustomTransformers,
        HandleValidation;
    
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

    /**
     * Must return all the data needed by the templates
     */
    public function fillTemplateWith(array $data, bool $isForm): array
    {
        return $data;
    }

    /**
     * Must return all the data needed by the fields of the form
     */
    public function fillFormWith(array $data): array
    {
        $data = collect($data)
            ->only($this->getDataKeys())
            ->all();
        
        return $this->transform($data);
    }

    abstract public function buildFormFields(FieldsContainer $formFields): void;

    abstract public function updateContent(array $data = []): array;

    /**
     * Build the optional Embed form layout.
     */
    public function buildFormLayout(FormLayoutColumn &$column): void
    {
    }

    final public function formLayout(): ?array
    {
        if ($fields = $this->fieldsContainer()->getFields()) {
            $column = new FormLayoutColumn(12);
            $this->buildFormLayout($column);

            if (empty($column->fieldsToArray()['fields'])) {
                foreach ($fields as $field) {
                    $column->withSingleField($field->key());
                }
            }

            return $column->fieldsToArray()['fields'];
        }

        return null;
    }

    final public function formConfig()
    {
        if ($this->pageAlertHtmlField === null) {
            return null;
        }

        return tap([], function (&$config) {
            $this->appendGlobalMessageToConfig($config);
        });
    }

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

    public function getDataLocalizations(): array
    {
        return [];
    }
}