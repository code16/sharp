<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Code16\Sharp\Utils\SharpNotification;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Traits\HandleLocalizedFields;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

abstract class SharpForm
{
    use HandleCustomBreadcrumb;
    use HandleFormFields;
    use HandleLocalizedFields;
    use HandlePageAlertMessage;
    use HandleValidation;
    use WithCustomTransformers;

    protected ?FormLayout $formLayout = null;
    protected bool $displayShowPageAfterCreation = false;
    protected ?string $editFormTitle = null;
    protected ?string $createFormTitle = null;

    final public function formLayout(): array
    {
        if ($this->formLayout === null) {
            $this->formLayout = new FormLayout();
            $this->buildFormLayout($this->formLayout);
        }

        return $this->formLayout->toArray();
    }

    public function formConfig(): array
    {
        return [];
    }

    final public function instance($id): array
    {
        return collect($this->find($id))
            // Filter model attributes on actual form fields
            ->only(
                array_merge(
                    $this->breadcrumbAttribute ? [$this->breadcrumbAttribute] : [],
                    $this->getDataKeys(),
                    array_keys($this->transformers),
                ),
            )
            ->all();
    }

    final public function newInstance(): array
    {
        return collect($this->create())
            // Filter model attributes on actual form fields
            ->only(
                array_merge(
                    $this->breadcrumbAttribute ? [$this->breadcrumbAttribute] : [],
                    $this->getDataKeys(),
                    array_keys($this->transformers),
                ),
            )
            ->all();
    }

    final public function store(array $data): mixed
    {
        return $this->update(null, $data);
    }

    public function buildFormConfig(): void {}

    protected function configureDisplayShowPageAfterCreation(bool $displayShowPage = true): self
    {
        $this->displayShowPageAfterCreation = $displayShowPage;

        return $this;
    }

    protected function configureEditTitle(string $editFormTitle): self
    {
        $this->editFormTitle = $editFormTitle;

        return $this;
    }

    protected function configureCreateTitle(string $createFormTitle): self
    {
        $this->createFormTitle = $createFormTitle;

        return $this;
    }
    
    final public function getCreateTitle(): ?string
    {
        return $this->createFormTitle;
    }
    
    final public function getEditTitle(): ?string
    {
        return $this->editFormTitle;
    }

    public function isDisplayShowPageAfterCreation(): bool
    {
        return $this->displayShowPageAfterCreation;
    }

    /**
     * Pack new Model data as JSON.
     */
    public function create(): array
    {
        $attributes = collect($this->getDataKeys())
            ->flip()
            ->map(fn () => null)
            ->all();

        // Build a fake Model class based on attributes
        return $this->transform(new class($attributes) extends \stdClass
        {
            public function __construct(private array $attributes)
            {
                foreach ($attributes as $name => $value) {
                    $this->$name = $value;
                }
            }

            public function toArray()
            {
                return $this->attributes;
            }
        });
    }

    /**
     * Display a notification next time entity list is shown.
     */
    public function notify(string $title): SharpNotification
    {
        return new SharpNotification($title);
    }

    /**
     * @deprecated use ->validate() or rules() methods instead; will be removed in 10.x
     */
    protected function getFormValidatorClass(): ?string
    {
        return property_exists($this, 'formValidatorClass')
            ? $this->formValidatorClass
            : null;
    }

    /**
     * Retrieve a Model for the form and pack all its data as JSON.
     */
    abstract public function find(mixed $id): array;

    /**
     * Update the Model of id $id with $data.
     */
    abstract public function update(mixed $id, array $data);

    /**
     * Build form fields.
     */
    abstract public function buildFormFields(FieldsContainer $formFields): void;

    /**
     * Build form layout.
     */
    abstract public function buildFormLayout(FormLayout $formLayout): void;
}
