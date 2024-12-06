<?php

namespace Code16\Sharp\EntityList\Commands\QuickCreate;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class QuickCreationCommand extends EntityCommand
{
    protected SharpForm $sharpForm;

    public function __construct(protected ?array $specificFormFields) {}

    public function label(): ?string
    {
        return null;
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

    public function execute(array $data = []): array
    {
        $this->sharpForm->update(null, $data);

        return $this->reload();
    }

    public function setFormInstance(SharpForm $form): void
    {
        $this->sharpForm = $form;
    }
}
