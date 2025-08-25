<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Fields\Formatters\AbstractSimpleFormatter;
use Code16\Sharp\Form\Fields\Formatters\ListFormatter;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\HasModalFormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFields;
use Code16\Sharp\Utils\Fields\HandleFormHtmlFields;
use Code16\Sharp\Utils\Icons\IconManager;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\View;

abstract class SharpFormEditorEmbed
{
    use HandleFields;
    use HandleFormHtmlFields;
    use HandlePageAlertMessage;
    use HandleValidation;
    use HasModalFormLayout;
    use WithCustomTransformers;

    protected ?string $label = null;
    protected ?string $tagName = null;
    protected ?string $icon = null;
    protected string|View|null $showTemplate = null;
    protected string|View|null $formTemplate = null;
    protected bool $displayEmbedHeader = true;
    protected ?string $embedHeaderTitle = null;

    public function toConfigArray(bool $isForm): array
    {
        $config = [
            'key' => $this->key(),
            'label' => $this->label ?: Str::snake(class_basename(get_class($this))),
            'tag' => $this->tagName(),
            'attributes' => collect($this->fields())->keys()->toArray(),
            'icon' => app(IconManager::class)->iconToArray($this->icon),
            'fields' => $this->fields(),
            'displayEmbedHeader' => $this->displayEmbedHeader,
            'embedHeaderTitle' => $this->embedHeaderTitle ?: $this->label,
        ];

        Validator::make($config, [
            'key' => ['required'],
            'label' => ['required'],
            'tag' => ['required', 'regex:/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/'],
            'attributes' => ['array'],
        ], [
            'attributes.required' => 'Your Embed should at least have one form field',
            'tag.regex' => 'the tag name should only contain letters, figures and carets',
        ])->validate();

        return $config;
    }

    public function buildEmbedConfig(): void {}

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
    public function buildFormLayout(FormLayoutColumn &$column): void {}

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

                if ($field->formatter() instanceof UploadFormatter) {
                    // in case of uploads we only want to call formatter on Form store/update
                    return $value;
                }

                if ($field->formatter() instanceof ListFormatter) {
                    $field->formatter()->formatItemFieldUsing(function (SharpFormField $itemField) {
                        if ($itemField instanceof SharpFormUploadField) {
                            return new class() extends AbstractSimpleFormatter {};
                        }

                        return $itemField->formatter();
                    });
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

    final protected function configureTemplate(string|View $template): self
    {
        $this->formTemplate = $template;
        $this->showTemplate = $template;

        return $this;
    }

    final protected function configureFormTemplate(string|View $template): self
    {
        $this->formTemplate = $template;

        return $this;
    }

    final protected function configureShowTemplate(string|View $template): self
    {
        $this->showTemplate = $template;

        return $this;
    }

    final protected function configureDisplayEmbedHeader(bool $display = true, ?string $title = null): self
    {
        $this->displayEmbedHeader = $display;
        $this->embedHeaderTitle = $title;

        return $this;
    }

    final public function transformDataWithRenderedTemplate(array $data, bool $isForm): array
    {
        $data = $this->transformDataForTemplate($data, $isForm);

        return [
            ...$data,
            '_html' => $this->renderTemplate($data, $isForm),
        ];
    }

    private function renderTemplate(array $data, bool $isForm): string
    {
        $template = $isForm ? $this->formTemplate : $this->showTemplate;

        if (! $template) {
            return '';
        }

        if (isset($data['slot'])) {
            $data['slot'] = new HtmlString($data['slot']);
        }

        return is_string($template)
            ? Blade::render($template, $data)
            : $template->with($data)->render();
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
