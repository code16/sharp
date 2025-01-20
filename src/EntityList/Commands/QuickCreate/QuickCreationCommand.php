<?php

namespace Code16\Sharp\EntityList\Commands\QuickCreate;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class QuickCreationCommand extends EntityCommand
{
    protected ?string $title = null;
    protected SharpForm $sharpForm;
    protected string $entityKey;
    protected mixed $instanceId;

    public function __construct(protected ?array $specificFormFields) {}

    public function label(): ?string
    {
        return $this->title;
    }

    public function buildCommandConfig(): void
    {
        $this
            ->configureFormModalTitle($this->title)
            ->configureFormModalButtonLabel(__('sharp::action_bar.form.submit_button.create'))
            ->configureFormModalSubmitAndReopenButton(__('sharp::entity_list.quick_creation_modal.create_and_reopen'));
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $this->sharpForm
            ->getBuiltFields()
            ->when(
                $this->specificFormFields,
                fn ($sharpFormFields) => $sharpFormFields
                    ->filter(fn (SharpFormField $field) => in_array($field->key, $this->specificFormFields))
            )
            ->each(fn (SharpFormField $field) => $formFields->addField($field));
    }

    protected function initialData(): array
    {
        return $this->sharpForm->create();
    }

    public function rules(): array
    {
        return method_exists($this->sharpForm, 'rules')
            ? $this->sharpForm->rules()
            : [];
    }

    public function messages(): array
    {
        return method_exists($this->sharpForm, 'messages')
            ? $this->sharpForm->messages()
            : [];
    }

    public function getDataLocalizations(): array
    {
        return $this->sharpForm->getDataLocalizations();
    }
    
    public function getInstanceId(): mixed
    {
        return $this->instanceId;
    }

    public function execute(array $data = []): array
    {
        $this->instanceId = $this->sharpForm->update(null, $data);
        $currentUrl = sharp()->context()->breadcrumb()->getCurrentSegmentUrl();

        return $this->sharpForm->isDisplayShowPageAfterCreation()
            ? $this->link(sprintf(
                '%s/s-show/%s/%s',
                $currentUrl,
                $this->entityKey,
                $this->instanceId
            ))
            : $this->reload();
    }

    public function setFormInstance(SharpForm $form): self
    {
        $this->sharpForm = $form;

        return $this;
    }

    public function setEntityKey(string $entityKey): self
    {
        $this->entityKey = $entityKey;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
