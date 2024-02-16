<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Fields\HandleFormFields;
use Code16\Sharp\Utils\SharpNotification;
use Code16\Sharp\Utils\Traits\HandleCustomBreadcrumb;
use Code16\Sharp\Utils\Traits\HandleLocalizedFields;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Traits\HandleValidation;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Support\Collection;

abstract class SharpForm
{
    use WithCustomTransformers;
    use HandleFormFields;
    use HandlePageAlertMessage;
    use HandleCustomBreadcrumb;
    use HandleLocalizedFields;
    use HandleValidation;

    protected ?FormLayout $formLayout = null;
    protected bool $displayShowPageAfterCreation = false;

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
        return tap(
            [
                'hasShowPage' => $this->displayShowPageAfterCreation,
            ],
            function (&$config) {
                $this->appendBreadcrumbCustomLabelAttribute($config);
            },
        );
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

    public function buildFormConfig(): void
    {
    }

    protected function configureDisplayShowPageAfterCreation(bool $displayShowPage = true): self
    {
        $this->displayShowPageAfterCreation = $displayShowPage;

        return $this;
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
            ->map(function () {
                return null;
            })
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

    final function getEditorFieldsWithEmbeddedUploads(): Collection
    {
        return collect($this->fieldsContainer()->getFields())
            ->filter(fn ($field) => $field instanceof SharpFormEditorField
                && $field->embedUploadsConfig() !== null
            );
    }

    /**
     * This method is called after a store operation, in case you need to perform
     * additional operations which needs the $instanceId.
     * You MUST handle there any Editor embedded upload with a path containing an {id} part.
     */
    public function handleDeferredStoreOperations($instanceId, array $data): void
    {
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
