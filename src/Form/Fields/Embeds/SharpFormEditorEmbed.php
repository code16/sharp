<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\HasModalFormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFields;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Str;

abstract class SharpFormEditorEmbed
{
    use HandleFields;
    use HandlePageAlertMessage;
    use WithCustomTransformers;
    use HandleValidation;
    use HasModalFormLayout;

    protected ?string $label = null;
    protected ?string $tagName = null;
    protected ?string $icon = null;
    protected array $templates = [];

    public function toConfigArray(bool $isForm): array
    {
        if (! $template = $this->templates[$isForm ? 'form' : 'show'] ?? ($this->templates['form'] ?? null)) {
            $template = 'Empty template';
        }

        $config = [
            'key' => $this->key(),
            'label' => $this->label ?: Str::snake(class_basename(get_class($this))),
            'tag' => $this->tagName(),
            'attributes' => collect($this->fields())->keys()->toArray(),
            'template' => $template,
            'icon' => $this->icon,
            'fields' => $this->fields(),
        ];

        $this->validate($config, [
            'key' => ['required'],
            'label' => ['required'],
            'tag' => ['required', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'attributes' => ['array'],
            'template' => ['required'],
        ], [
            'attributes.required' => 'Your Embed should at least have one form field',
            'tag.regex' => 'the tag name should only contain letters, figures and carets',
        ]);

        return $config;
    }

    public function buildEmbedConfig(): void
    {
    }

    /**
     * Must return all the data needed by the templates.
     */
    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $data;
    }

    /**
     * Must return all the data needed by the fields of the form.
     */
    public function transformDataForFormFields(array $data): array
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
        return $this->modalFormLayout(function (FormLayoutColumn $column) {
            $this->buildFormLayout($column);
        });
    }

    /**
     * Applies Field Formatters on $data.
     */
    final public function formatRequestData(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $key) => in_array($key, $this->getDataKeys()))
            ->map(function ($value, $key) {
                if (! $field = $this->findFieldByKey($key)) {
                    return $value;
                }

                if (is_a($field, SharpFormUploadField::class)) {
                    // Uploads are a bit different in this case
                    $field->formatter()->setAlwaysReturnFullObject();
                }

                // Apply formatter based on field configuration
                return $field
                    ->formatter()
                    ->setDataLocalizations($this->getDataLocalizations())
                    ->fromFront($field, $key, $value);
            })
            ->toArray();
    }

    final protected function transformForTemplate(array $data): array
    {
        // This transformer is used for template and tag attributes. Here we should apply
        // custom use defined transformers, but ignore field formatters
        return $this->applyTransformers($data);
    }

    final protected function configureLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    final protected function configureTagName(string $tagName): self
    {
        $this->tagName = $tagName;

        return $this;
    }

    final protected function configureIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    final protected function configureFormInlineTemplate(string $template): self
    {
        return $this->setTemplate($template, 'form');
    }

    final protected function configureShowInlineTemplate(string $template): self
    {
        return $this->setTemplate($template, 'show');
    }

    final protected function configureFormTemplatePath(string $templatePath): self
    {
        return $this->setTemplate(
            file_get_contents(resource_path('views/'.$templatePath)),
            'form',
        );
    }

    final protected function configureShowTemplatePath(string $templatePath): self
    {
        return $this->setTemplate(
            file_get_contents(resource_path('views/'.$templatePath)),
            'show',
        );
    }

    private function setTemplate(string $template, string $key): self
    {
        $this->templates[$key] = $template;

        return $this;
    }

    final public function key(): string
    {
        return Str::replace('\\', '.', get_class($this));
    }
    
    final public function tagName(): string
    {
        return $this->tagName ?: 'x-'.Str::kebab(class_basename(get_class($this)));
    }
    
    public function getDataLocalizations(): array
    {
        return [];
    }
}
